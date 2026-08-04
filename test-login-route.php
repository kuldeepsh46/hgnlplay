<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/login', 'GET');
$response = $kernel->handle($request);

echo "STATUS: " . $response->getStatusCode() . "\n";
echo "---BODY (first 1500 chars)---\n";
echo substr($response->getContent(), 0, 1500) . "\n";

$kernel->terminate($request, $response);