<?php

namespace App\Console\Commands;

use App\Jobs\ChannelProcess;
use App\Models\Category;
use App\Models\Creator;
use App\Models\CreatorInfo;
use Cache;
use DOMDocument;
use DOMXpath;
use Illuminate\Console\Command;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SocialBlade
 * @package App\Console\Commands
 * @author Cookie
 */
class SocialBlade extends Command
{
    const DAYS_TO_CACHE_HTML_CONTENT = 5;
    const CACHE = "SB_dom";
    const URLS = [
        "https://socialblade.com/youtube/top/trending/top-500-channels-1-day/most-subscribed",
        "https://socialblade.com/youtube/top/trending/top-500-channels-30-days/most-subscribed",
        "https://socialblade.com/youtube/top/country/us",
        "https://socialblade.com/youtube/top/country/gb",
        "https://socialblade.com/youtube/top/country/de",
        "https://socialblade.com/youtube/top/country/fr",
        "https://socialblade.com/youtube/top/country/sk",
        "https://socialblade.com/youtube/top/country/cz",
    ];

    protected $signature = 'socialblade:grab';
    protected $description = '';
    private $client;

    /**
     * SocialBlade constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    private function fileOutput($fn, $stream)
    {
        $f = fopen($fn, 'w');
        fputs($f, $stream);
        fclose($f);
    }

    /**
     * @return int
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        $options = [
            'headers'        => null,
            'country_code'   => null,
            'device_type'    => null,
            'premium'        => null,
            'render'         => null,
            'session_number' => null,
            'autoparse'      => null,
            'retry'          => null,
            'timeout'        => null,
        ];

        // @todo make service, helpers and config?
        $client = new \ScraperAPI\Client(config('scraper.api_key'));

        foreach (self::URLS as $url) {
            $this->alert("==============$url=============");
            $this->info("check $url");

            if (!$html = Cache::get($cacheName = self::CACHE . $url)) {
                $html = $client->get($url, $options)->raw_body;
                Cache::set($cacheName, $html, now()->addDays(self::DAYS_TO_CACHE_HTML_CONTENT));
                echo "write\n";
            }

            $this->info("done");

            // temp
            //$this->fileOutput('test.html', $html);

            $dom = new DOMDocument();
            $dom->loadHTML($html, LIBXML_NOERROR);

            $path = "/html/body/div[10]/div[2]/div";
            $xpath = new DOMXpath($dom);
            $wrapper = $xpath->query($path);

            $this->info("Found {$wrapper->length} items.");

            for ($i = 4; $i < $wrapper->length; $i++) {

                if ($i < 14)
                    $shortUrl = $wrapper->item($i)->childNodes->item(5)->childNodes->item(3)->attributes->item(0)->nodeValue;
                else
                    $shortUrl = $wrapper->item($i)->childNodes->item(5)->childNodes->item(1)->attributes->item(0)->nodeValue;
//https://youtube.com/user/98hwv8nvcv0ab0bxley5lg
                $url = str_replace('/youtube', 'https://youtube.com', $shortUrl);

                $this->info($url);

                // just grab youtube channel url and fire the job
                ChannelProcess::dispatch($url)->delay(now()->addSeconds($i*20));
            }
        }

        return 0;
    }
}
