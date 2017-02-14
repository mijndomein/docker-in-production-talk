<?php

use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

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

    try {
        $response = ($app['apiClient'])->request('GET', $app['parameters']['dateApiService'], ['timeout' => 2]);
        $content = json_decode($response->getBody()->getContents());
        $date = $content->date;
    } catch (RequestException $e) {
        $date = null;
    }
    if (!isset($app['parameters']['healthy']) || $app['parameters']['healthy'] !== "yes") {
        return new Response('service is not healthy', 500);
    }

    return $app['twig']->render('demo.twig', [
        'title' => $app['parameters']['title'],
        'subTitle' => $app['parameters']['subTitle'] . ' ' . $date
    ]);
});

$app->run();
