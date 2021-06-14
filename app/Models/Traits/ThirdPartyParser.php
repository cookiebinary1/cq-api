<?php

namespace App\Models\Traits;

use App\Models\Category;
use App\Models\Creator;
use App\Models\CreatorInfo;
use App\Models\Image;
use App\Models\Source;
use Google_Service_YouTube_Channel;
use Google_Service_YouTube_ChannelListResponse;
use Google_Service_YouTube_ChannelSnippet;
use Google_Service_YouTube_SearchResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Performance\Performance;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Trait ThirdPartyParser
 * @package App\Traits
 * @author Cookie
 */
trait ThirdPartyParser
{
    public static string $youtubeChannelIdField = 'youtubeChannelId';
    public static string $spotifyArtistIdField = 'spotifyArtistIdField';
    public static string $twitchUserId = 'twitchUserId';

    /**
     * @param $youtubeChannelId
     * @return Model|Builder|CreatorInfo|null
     */
    static public function youtubeChannel($youtubeChannelId): Model|Builder|CreatorInfo|null
    {
        return (CreatorInfo::whereField(self::$youtubeChannelIdField)
            ->whereValue($youtubeChannelId)
            ->first()
        )?->creator;
    }

    /**
     * @param $spotifyArtistId
     * @return mixed
     */
    static public function spotifyArtist($spotifyArtistId): mixed
    {
        return (CreatorInfo::whereField(self::$spotifyArtistIdField)
            ->whereValue($spotifyArtistId)
            ->first()
        )?->creator;
    }

    /**
     * @param $soundcloudUrl
     * @return null|Creator
     */
    static public function soundcloudArtist($soundcloudUrl): null|Creator
    {
        return (CreatorInfo::whereField('url')
            ->whereValue($soundcloudUrl)
            ->first()
        )?->creator;
    }

    /**
     * @param $twitchUserId
     * @return Creator|null
     */
    static public function twitchUser($twitchUserId): Creator|null
    {
        return (CreatorInfo::whereField(self::$twitchUserId)
            ->whereValue($twitchUserId)
            ->first()
        )?->creator;
    }


    /**
     * @param $mixcloudUsername
     * @return Creator|null
     */
    static public function mixcloudUser($mixcloudUsername): Creator|null
    {
        return (CreatorInfo::whereField("username")
            ->whereValue($mixcloudUsername)
            ->first()
        )?->creator;
    }

}
