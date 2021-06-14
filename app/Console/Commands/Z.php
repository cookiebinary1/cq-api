<?php

namespace App\Console\Commands;

use App\Helpers\UrlSniffer;
use App\Models\User;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redis;

/**
 * Class UsersFaker
 * @package App\Console\Commands
 * @author Cookie
 */
class Z extends Command
{
    protected $signature = 'z';
    protected $description = 'Just Z it';

    /**
     * UsersFaker constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle()
    {
        for ($i=1; $i<10000; $i++) {
            \Cache::set("asdf$i","ccccc$i");
        }


        dd("tu som", \Cache::get("asdf55"));
//        app(UrlSniffer::class)->fire("https://open.spotify.com/artist/6wMr4zKPrrR0UVz08WtUWc");

        return 0;
    }
}
