<pre><?php

ini_set("display_errors", 1);

// include your composer dependencies
require_once '../vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName("Client_Library_Examples");
//$client->setDeveloperKey("AIzaSyCfJQFU9PXVT6CIsumyPu-WoRa3zPKKows");
$client->setDeveloperKey("AIzaSyBj5pzFnTDm8oXA4TZRfCTzMMVlE4EfiUA");

$service = new Google_Service_YouTube($client);

$queryParams = [
    'maxResults' => 25,
    'q'          => 'cookie_binary',
    'type'       => 'channel',
];

$time = microtime(1);
$response = $service->search->listSearch('snippet', $queryParams);

echo microtime(1)-$time;
//$response = $service->activities->listActivities('snippet,contentDetails', $queryParams);

print_r($response);

