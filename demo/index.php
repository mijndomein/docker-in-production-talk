<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
]);

$app->get('/', function () use ($app) {
    $title = "hello world";
    $subTitle = "demo";
    return $app['twig']->render('demo.twig', [
        'title' => $title,
        'subTitle' => $subTitle
    ]);
});

$app->run();
