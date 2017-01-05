<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    $response = ($app['apiClient'])->request('GET', $app['parameters']['dateApiService']);
    $date = json_decode($response->getBody()->getContents());

    if (!isset($app['parameters']['healthy'])) {
        return new Response('service is not healthy', 500);
    }

    return $app['twig']->render('demo.twig', [
        'title' => $app['parameters']['title'],
        'subTitle' => $app['parameters']['subTitle'] . ' ' . $date->date
    ]);
});

$app->run();
