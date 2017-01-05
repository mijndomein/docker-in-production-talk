<?php

require_once __DIR__.'/../vendor/autoload.php';

$configuration = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__ . "/config/parameters.yml"));

$app = new Silex\Application();
$app['parameters'] = $configuration['parameters'];

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
]);

$app->get('/', function () use ($app) {
    return $app['twig']->render('demo.twig', [
        'title' => $app['parameters']['title'],
        'subTitle' => $app['parameters']['subTitle']
    ]);
});

$app->run();
