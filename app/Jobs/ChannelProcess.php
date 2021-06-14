<?php

namespace App\Jobs;

use App\Helpers\UrlSniffer;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ChannelProcess
 * @package App\Jobs
 * @author Cookie
 */
class ChannelProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $channelUrl;
    protected Client $client;

    /**
     * ChannelProcess constructor.
     * @param $channelUrl
     */
    public function __construct($channelUrl)
    {
        $this->channelUrl = $channelUrl;
        $this->client = new Client();
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Throwable
     * @throws \TwitchApi\Exceptions\ClientIdRequiredException
     * @throws \TwitchApi\Exceptions\EndpointNotSupportedByApiVersionException
     * @throws \TwitchApi\Exceptions\InvalidIdentifierException
     */
    public function handle()
    {
        app(UrlSniffer::class)->fire($this->channelUrl);
    }
}
