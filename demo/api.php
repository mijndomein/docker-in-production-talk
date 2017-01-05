<?php

use Symfony\Component\HttpFoundation\JsonResponse;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function () use ($app) {
    return new JsonResponse(['date' => date('d-m-Y')]);
});

$app->run();
