<?php

namespace App\Console\Commands;

use App\Models\Creator;
use App\Models\Source;
use App\Models\Traits\ThirdPartyParser;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Performance\Performance;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;
use TwitchApi\Exceptions\ClientIdRequiredException;
use TwitchApi\Exceptions\InvalidLimitException;
use TwitchApi\Exceptions\InvalidOffsetException;
use TwitchApi\Exceptions\InvalidTypeException;
use TwitchApi\Exceptions\TwitchApiException;

/**
 * Class Twitch
 * @package App\Console\Commands
 * @author Cookie
 */
class Twitch extends Command
{
    protected $signature = 'twitch:grab';
    protected $description = 'Parse and grab Twitch channels and process in background jobs.';


    /**
     * Twitch constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Creator|ThirdPartyParser|HigherOrderBuilderProxy|Model|int|mixed
     * @throws InvalidArgumentException
     * @throws ClientIdRequiredException
     * @throws InvalidLimitException
     * @throws InvalidOffsetException
     * @throws InvalidTypeException
     * @throws TwitchApiException
     * @throws Throwable
     */
    public function handle()
    {

        $bar = $this->output->createProgressBar(100 * (ord('z') - ord('a') + 1));
        $bar->start();

        foreach (range(ord('a'), ord('z')) as $code) {

            $searchQuery = chr($code);

            $result = cache(
                'twitch_search_channel_' . $searchQuery,
                fn() => twitch_api()->searchChannels($searchQuery, 100)
            );

            foreach ($result['channels'] as $channelInfo) {

                $channelInfo[ThirdPartyParser::$twitchUserId] = $channelInfo['_id'];


                $creator = (Creator::twitchUser($channelInfo['_id']) ?? Creator::createNew())
                    ->updateComplex([
                        'data'     => ['name'        => $channelInfo['display_name'],
                                       'description' => $channelInfo['status']],
                        'info'     => $channelInfo,
                        'source' => 'twitch',
                        'country'  => $channelInfo['language'],
                        'image'    => ['url'  => $channelInfo['logo'],
                                       'data' => 'N/A'],
                    ]);

                $bar->advance();

                \Log::info("TWITCH USER added/updated: \n" . $creator->name);
            }
        }

        $bar->finish();
        $this->info("\n\nDONE.\n");

        return 0;
    }
}
