<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__.'/storage/framework/maintenance.php')) {
    require __DIR__.'/storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__.'/vendor/autoload.php')) {
    require __DIR__.'/vendor/autoload.php';
} elseif (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    die('Folder vendor tidak ditemukan. Silakan upload folder vendor atau jalankan composer install.');
}

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__.'/bootstrap/app.php')) {
    $app = require_once __DIR__.'/bootstrap/app.php';
} else {
    $app = require_once __DIR__.'/../bootstrap/app.php';
}

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
