<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$request = Request::create('/risks/smap?tab=dashboard', 'GET');
$response = $kernel->handle($request);
if ($response->exception) {
    file_put_contents('trace.txt', $response->exception->getMessage()."\n".$response->exception->getTraceAsString());
}
