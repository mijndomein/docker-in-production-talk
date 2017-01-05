<?php

require_once __DIR__.'/../vendor/autoload.php';

$configuration = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__ . "/config/parameters.yml"));

$guzzleClient = new GuzzleHttp\Client();

$app = new Silex\Application();
$app['parameters'] = $configuration['parameters'];
$app['apiClient'] = $guzzleClient;

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
]);

$app->get('/', function () use ($app) {
    $response = ($app['apiClient'])->request('GET', 'http://api.service.consul');
    /**
     * @var \GuzzleHttp\Psr7\Response $response
     */
    $date = json_decode($response->getBody()->getContents());

    return $app['twig']->render('demo.twig', [
        'title' => $app['parameters']['title'],
        'subTitle' => $app['parameters']['subTitle'] . ' ' . $date->date
    ]);
});

$app->run();
