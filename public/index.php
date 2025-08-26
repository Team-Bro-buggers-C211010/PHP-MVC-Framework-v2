<?php

use Core\Http\Request;
use Core\Http\Response;
use Core\Router\Router;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ .'/../loadenv.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ .'/../framework/Startup.php';

$request = new Request();
$response = new Response();

$response->setHeader('Access-Control-Allow-Origin: http://localhost:5173');
$response->setHeader('Access-Control-Allow-Credentials: true');
$response->setHeader('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
$response->setHeader('Access-Control-Allow-Headers: Content-Type, Authorization');
$response->setHeader('Content-Type: application/json; charset=UTF-8');

$router = new Router($response, $request);

require_once __DIR__ . '/../routes/web.php';

$router->resolve($request->getUrl(), $request->getMethod());

$response->render();