<?php

namespace App\Console\Commands;

use App\Http\Controllers\CollabController;
use App\Http\Controllers\CreatorController;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

/**
 * Class CacheRefresh
 * @package App\Console\Commands
 * @author Cookie
 */
class CacheRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh API cached results.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $route
     * @param $method
     * @param $parameters
     * @throws BindingResolutionException
     */
    private function handleRequest($route, $method, $parameters)
    {
        $this->info($route . "..");

        $request = Request::create(route($route), $method, $parameters);
        $res = app()->make(Kernel::class)->handle($request);

        $this->info("..OK");
    }

    /**
     * @return int
     * @throws BindingResolutionException
     */
    public function handle(): int
    {
        $sources = range(1, 5);
        foreach ($sources as $source) {
            $scopes = "creator1.image,creator2.image,userLikes";

            $this->handleRequest('collab.index', 'GET', [
                'trendingRange' => "all",
                'scopes'        => $scopes,
                'sources'       => $source]);

            $this->handleRequest('collab.index', 'GET', [
                'trendingRange' => "week",
                'scopes'        => $scopes,
                'sources'       => $source]);

            $this->handleRequest('creator.index', 'GET', [
                'trendingRange' => "week",
                'scopes'        => "image,sources",
                'sources'       => $source]);

        }
        return 0;
    }
}
