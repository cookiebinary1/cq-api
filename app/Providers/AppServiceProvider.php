<?php

namespace App\Providers;

use App\Helpers\UrlSniffer;
use Auth0\Login\Repository\Auth0UserRepository;
use FusionAuth\FusionAuthClient;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @author Cookie
     */
    public function register()
    {
//
//        $this->app->bind(
//            Auth0UserRepositoryAbstract::class,
//            Auth0UserRepository::class
//        );

        $this->app->singleton(Google_Client::class, function () {
            $client = new Google_Client();
            $client->setApplicationName("Collabio_Client");
            $apiKey = config('youtube.key');
            $client->setDeveloperKey($apiKey);

            return $client;
        });

        $this->app->singleton(Google_Service_YouTube::class, function () {
            return new Google_Service_YouTube(get_google_client());
        });

        $this->app->singleton(UrlSniffer::class, function () {
            return new UrlSniffer();
        });

        $this->app->bind(FusionAuthClient::class, function ($app) {
            return new FusionAuthClient(config('fusionauth.api_key'), config('fusionauth.base_url'));
        });
    }
//0EhO6zJ_Leh1te4wXugB-ymip4rCOCJPsf1_y0m9z90

//U0cua0M0YmZqbjBTRjZvZXM1R2N5bXR6US5vT204eld6Tmk0NkN4Skh2bE9jZVpx
//RXg3SXBlVUJKdS1WQ0xlZVE5U2FN

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.https')) {
            URL::forceScheme('https');
        }
    }
}
