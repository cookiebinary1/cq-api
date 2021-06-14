<?php

/*
|--------------------------------------------------------------------------
| Code in bootstrap/environment.php
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Dotenv package is not accessible in the file so we will use the other way around.
| Create an instance of Dotenv class which takes two parameters:
| $dotenv = new Dotenv\Dotenv(path_to_env_file, name_of_file);
|
*/

use Dotenv\Dotenv;

$envPath = realpath(dirname(__DIR__));

if (file_exists($envPath . '/.env.local')) {
    (Dotenv::createImmutable($envPath, '.env.local'))->load();
}
(Dotenv::createImmutable($envPath, '.env'))->load();
