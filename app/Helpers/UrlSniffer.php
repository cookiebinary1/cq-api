<?php

namespace App\Helpers;

use App\Models\Creator;
use App\Models\Traits\ThirdPartyParser;
use Cache;
use ErrorException;
use Google_Service_YouTube_Channel;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Log;
use Psr\SimpleCache\InvalidArgumentException as InvalidArgumentException2;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;
use TwitchApi\Exceptions\ClientIdRequiredException;
use TwitchApi\Exceptions\EndpointNotSupportedByApiVersionException;
use TwitchApi\Exceptions\InvalidIdentifierException;
use TwitchApi\TwitchApi;
use Illuminate\Http\Response as ResponseAlias;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Foundation\Application;

class UrlSniffer
{
    /**
     * @param $url
     * @return mixed
     * @throws ClientIdRequiredException
     * @throws EndpointNotSupportedByApiVersionException
     * @throws InvalidArgumentException2
     * @throws InvalidIdentifierException
     * @throws Throwable
     */
    function fire($url): mixed
    {
        $creator = match (only_host_name($url)) {
            "youtube.com" => $this->fireYoutube($url),
            'spotify.com' => $this->fireSpotify($url),
            'soundcloud.com' => $this->fireSoundCloud($url),
            'twitch.tv' => $this->fireTwitch($url),
            'mixcloud.com' => $this->fireMixcloud($url),
        };

        if (!$creator instanceof Creator) {
            Log::info("Creator creation failed: ", [$creator ?? null]);

            return null;
        }

        Log::info("Creator created: ", $creator->toArray());

        return $creator->recomputePriority();
    }

    /**
     * @param $url
     * @return Creator|ThirdPartyParser|Application|ResponseFactory|Model|ResponseAlias|null
     * @throws InvalidArgumentException2
     * @throws Throwable
     */
    public function fireYoutube($url)
    {
        $url = str_replace("music.youtube.com", "www.youtube.com", $url);

        // Cache youtube channel web page
        if (!$content = Cache::get($url)) {
            try {
                Cache::set($url, $content = file_get_contents($url), now()->addWeeks(2));
            } catch (ErrorException $exception) {
                // do nothing
            }
        }

        if (!$content) {
            return null;
        }

        $crawler = new Crawler();
        $crawler->addContent($content);

        try {
            $channelId = $crawler->filter("meta[itemprop=channelId]")->first()->attr('content');
        } catch (InvalidArgumentException $exception) {
            Log::error('ERROR on YT get channelId on url: ' . $url);

            return null;
//            return error(9001, null, null, ['url' => $url]);
        }

        if (!$channelId) {
            Log::error('ERROR on YT get channelId on url: ' . $url);

            return null;
        }

        Log::info("YT channel ID: $channelId");

        // Cache API request / response
        if (!$channelListResponse = Cache::get("youtube_api_channel_$channelId")) {
            $channelListResponse = get_google_service_youtube()
                ->channels
                ->listChannels('snippet,contentDetails,statistics',
                    [
                        'id' => $channelId,
                    ]);

            Cache::set("youtube_api_channel_$channelId", $channelListResponse, now()->addWeeks(2));
        }

        $creator = null;

        /** @var Google_Service_YouTube_Channel $item */
        foreach ($channelListResponse->getItems() as $item) {
            $snippet = $item->getSnippet();
            $statistics = $item->getStatistics();

            $channelId = $item->getId();
            $url = "https://youtube.com/channel/$channelId"; // @todo check short url

            $creator = (Creator::youtubeChannel($channelId) ?? Creator::createNew())
                ->updateComplex([
                    'data'    => ['name'        => $snippet->getTitle(),
                                  'description' => $snippet->getDescription()],
                    'info'    => ['url'                           => $url,
                                  'subscriberCount'               => $statistics->getSubscriberCount(),
                                  'videoCount'                    => $statistics->getVideoCount(),
                                  'viewCount'                     => $statistics->getViewCount(),
                                  Creator::$youtubeChannelIdField => $channelId],
                    'source'  => 'youtube',
                    'country' => $snippet->getCountry(),
                    'image'   => ['url'  => $snippet->getThumbnails()->getMedium()->getUrl(),
                                  'data' => (array)$snippet->getThumbnails()->getMedium()->toSimpleObject()]
                ]);
        }

        return $creator;
    }

    /**
     * @param $ATUrl
     * @return mixed
     * @throws InvalidArgumentException2
     */
    public function fireSpotify($ATUrl): mixed
    {
        preg_match_all('/https:\/\/open\.spotify\.com\/artist\/([^?]*)/m', $ATUrl, $matches, PREG_SET_ORDER,);

        if (!$artistId = @$matches[0][1]) {
            return null;
        }

        // Cache Spotify API request / response
        if (!$artist = Cache::get("spotify_artist_$artistId")) {
            $artist = spotify_api()->getArtist($artistId);
            Cache::set("spotify_artist_$artistId", $artist, now()->addWeeks(2));
        }

        // get spotify access token
//        $ATUrl = "https://open.spotify.com/get_access_token?reason=transport&productType=web_player";
//        $accessToken = \Http::get($ATUrl)->json()['accessToken'];
//
//        $variables = "{\"uri\":\"spotify:artist:{$artistId}\"}";
//        $url = "https://api-partner.spotify.com/pathfinder/v1/query?operationName=queryArtistOverview&variables=" . urlencode($variables)
//            . "&extensions=%7B%22persistedQuery%22%3A%7B%22version%22%3A1%2C%22sha256Hash%22%3A%2253f2fcff0a0f47530d71f576113ed9db94fc3ccd1e8c7420c0852b828cadd2e0%22%7D%7D";
//        $data = \Http::withToken($accessToken)
//            ->withHeaders(["app-platform" => "WebPlayer"])
//            ->withHeaders(["authorization" => "Bearer $accessToken"])
//            ->post($url);
//        dd($data);
        // authorization: Bearer BQClmcMMOTT-2w9IBDvYFN6UvSJPD5z9SbYdF9JjVcWGF5Hf2JdZsrn71AFkZAHCsN5n3uaPxBduu8vvSuQ
        // https://api-partner.spotify.com/pathfinder/v1/query?operationName=queryArtistOverview&variables=%7B%22uri%22%3A%22spotify%3Aartist%3A6m8itYST9ADjBIYevXSb1r%22%7D&extensions=%7B%22persistedQuery%22%3A%7B%22version%22%3A1%2C%22sha256Hash%22%3A%2253f2fcff0a0f47530d71f576113ed9db94fc3ccd1e8c7420c0852b828cadd2e0%22%7D%7D

        // https://api-partner.spotify.com/pathfinder/v1/query?operationName=queryArtistOverview&variables=%7B%22uri%22%3A%22spotify%3Aartist%3A21mKp7DqtSNHhCAU2ugvUw%22%7D&extensions=%7B%22persistedQuery%22%3A%7B%22version%22%3A1%2C%22sha256Hash%22%3A%2253f2fcff0a0f47530d71f576113ed9db94fc3ccd1e8c7420c0852b828cadd2e0%22%7D%7D


        return (Creator::spotifyArtist($artistId) ?? Creator::createNew())
            ->updateComplex([
                'data'   => ['name'        => $artist->name,
                             'description' => implode(", ", $artist->genres) . " " . $artist->type],
                'info'   => ['url'                          => $ATUrl,
                             'followers'                    => $artist->followers->total,
                             'popularity'                   => $artist->popularity,
                             'genres'                       => implode(", ", $artist->genres),
                             Creator::$spotifyArtistIdField => $artist->id],
                'source' => 'spotify',
                'image'  => ['url'  => $artist->images[1]->url,
                             'data' => (array)$artist->images[1]]
                // Country is not available
            ]);
    }

    /**
     * @param $url
     * @return Creator|ThirdPartyParser|HigherOrderBuilderProxy|Model|mixed
     * @throws InvalidArgumentException2
     * @throws Throwable
     */
    public function fireSoundCloud($url)
    {
        // Cache artist web page
        if (!$content = Cache::get($url)) {
            Cache::set($url, $content = file_get_contents($url), now()->addWeeks(2));
        }

        $crawler = new Crawler(null, $url);
        $crawler->addContent($content, null);

        $name = $crawler->filter("header > h1 > a")->text();
        $imageUrl = $crawler->filter("header > img")->attr('src');
        $metaDesc = $crawler->filter("meta[name=description]")->attr('content');
        preg_match_all('/(\d*)\sFollowers/m', $metaDesc, $matches, PREG_SET_ORDER, 0);
        $followers = $matches[0][1];
        $description = $crawler->filter("p[itemprop=description]")->text();

        return (Creator::soundcloudArtist($url) ?? Creator::createNew())
            ->updateComplex([
                'data'   => ['name'        => $name,
                             'description' => $description],
                'info'   => ['url'       => $url,
                             'followers' => $followers],
                'source' => 'soundcloud',
                'image'  => ['url'  => $imageUrl,
                             'data' => 'N/A']
            ]);
    }

    /**
     * @param $url
     * @return ResponseAlias|Creator|Application|ResponseFactory
     * @throws ClientIdRequiredException
     * @throws EndpointNotSupportedByApiVersionException
     * @throws InvalidArgumentException2
     * @throws InvalidIdentifierException
     * @throws Throwable
     */
    public function fireTwitch($url): ResponseAlias|Creator|Application|ResponseFactory
    {
        $options = [
            'client_id'     => config('twitch.client_id'),
            'client_secret' => config('twitch.client_secret'),
        ];

        preg_match_all('/twitch.tv\/([^\/]*)/m', $url, $matches, PREG_SET_ORDER, 0);
        $username = $matches[0][1];

        // Cache twitch API request / response
        // @todo CACHE THIS
        $twitchApi = new TwitchApi($options);
        $response = $twitchApi->getUserByUsername($username);
        if (!$user = $response['users'][0] ?? null) {
            return error(9002, additionalData: compact('username', 'response'));
        }

        $channelInfo = $twitchApi->getChannel($user['_id']);
        $channelInfo['url'] = "https://www.twitch.com/$user[name]";
        $channelInfo[ThirdPartyParser::$twitchUserId] = $user['_id'];

//        dd($user, $channelInfo, $twitchApi->getChannelCollection($user['_id']));
//        dd($twitchApi->getChannelVideos($user['_id']));
        //dd($user, $channelInfo);

        return (Creator::twitchUser($user['_id']) ?? Creator::createNew())
            ->updateComplex([
                'data'    => ['name'        => $user['display_name'],
                              'description' => $user['bio']],
                'info'    => $channelInfo,
                'source'  => 'twitch',
                'country' => $channelInfo['language'],
                'image'   => ['url'  => $user['logo'],
                              'data' => 'N/A'],
            ]);
    }

    /**
     * @param $url
     * @return Creator|Application|ResponseFactory|HigherOrderBuilderProxy|Model|ResponseAlias|mixed
     * @throws InvalidArgumentException2
     * @throws Throwable
     */
    public function fireMixcloud($url)
    {
        $re = '/^http[s]\:\/\/(www\.)*mixcloud\.com\/([^\/]*)/m';

        preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);

        if (!$slug = @$matches[0][2]) {
            return error(9003, additionalData: compact('url'));
        }

        $apiUrl = "https://api.mixcloud.com/" . $slug;
        // @todo use scraperAPI instead of direct grabbing
        $data = json_decode(file_get_contents($apiUrl), 1);

        return (Creator::mixcloudUser(@$data['username']) ?? Creator::createNew())
            ->updateComplex([
                'data'    => ['name'        => @$data['username'],
                              'description' => @$data['biog']],
                'info'    => $data,
                'source'  => 'mixcloud',
                'country' => @$data['country'],
                'image'   => ['url'  => @$data['pictures']['320wx320h'],
                              'data' => 'N/A'],
            ]);
    }
}
