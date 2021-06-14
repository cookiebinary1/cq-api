<?php

use App\Http\Controllers\LoginUser;
use App\Http\Controllers\LogoutUser;
use App\Http\Controllers\RegisterUser;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\TestController;
use App\Helpers\UrlSniffer;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use TwitchApi\TwitchApi;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return [
        'app' => 'Collabio RestAPI',
        'version' => config('app.version'),
        'message' => 'Welcome!',
    ];
})->name('welcome');


Route::get('test', TestController::class);
Route::get('test2/{question:slug}', TestController::class . "@test2")->name("test2");

Route::get('solr-test', function () {

    include "bootstrap.php";

    $options = array
    (
        'hostname' => SOLR_SERVER_HOSTNAME,
        'login' => SOLR_SERVER_USERNAME,
        'password' => SOLR_SERVER_PASSWORD,
        'port' => SOLR_SERVER_PORT,
    );

    $client = new SolrClient($options);

    $doc = new SolrInputDocument();

    $doc->addField('id', 334455);
    $doc->addField('cat', 'Software');
    $doc->addField('cat', 'Lucene');

    $updateResponse = $client->addDocument($doc);

    print_r($updateResponse->getResponse());


})->name('test');


Route::get("facebook-privacy-policy", function () {
    return "TODO";
});
