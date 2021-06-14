<?php
/**
 * @author Cookie
 */


use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use ImageKit\ImageKit;
use ScraperAPI\Client;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use TwitchApi\Exceptions\ClientIdRequiredException;
use TwitchApi\TwitchApi;

if (!function_exists('get_google_client')) {
    /**
     * @return Google_Client|Application|mixed
     */
    function get_google_client()
    {
        return app(Google_Client::class);
    }
}

if (!function_exists('get_google_service_youtube')) {
    /**
     * @return Google_Service_YouTube
     */
    function get_google_service_youtube(): Google_Service_YouTube
    {
        return app(Google_Service_YouTube::class, [get_google_client()]);
    }
}

if (!function_exists('spotify_api')) {
    /**
     * @return SpotifyWebAPI
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function spotify_api(): SpotifyWebAPI
    {
        if (!$accessToken = Cache::get('spotify_access_token')) {
            $session = new Session(
                config('spotify.client_id'),
                config('spotify.client_secret'),
            );
            $session->requestCredentialsToken();
            $accessToken = $session->getAccessToken();

            Cache::set('spotify_access_token', $accessToken, Carbon::parse($session->getTokenExpiration()));
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        return $api;
    }
}

if (!function_exists('twitch_api')) {
    /**
     * @return TwitchApi
     * @throws ClientIdRequiredException
     */
    function twitch_api(): TwitchApi
    {
        return new TwitchApi([
            'client_id'     => config('twitch.client_id'),
            'client_secret' => config('twitch.client_secret'),
        ]);
    }
}

if (!function_exists('imagekit_api')) {
    /**
     * @return ImageKit
     */
    function imagekit_api(): ImageKit
    {
        return new ImageKit(
            config('imagekit.public_key'),
            config('imagekit.private_key'),
            config('imagekit.endpoint'),
        );
    }
}

if (!function_exists('scraper_api')) {
    /**
     * @return Client
     */
    function scraper_api()
    {
        return new Client(config('scraper.api_key'));
    }
}


if (!function_exists('mixcloud_api')) {

    function mixcloud_api()
    {
        $client = new \App\Helpers\Mixcloud(config('mixcloud.client_id'), config('mixcloud.client_secret'));
        $client->getAuthorizationUri();


    }
}

