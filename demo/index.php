<?php

namespace Mijndomein\Demo;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Mijndomein\Demo\Events\DateNotAvailableEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__.'/../vendor/autoload.php';

$configuration = Yaml::parse(
    file_get_contents(__DIR__ . "/config/parameters.yml")
);

$app = new Application();
$guzzleClient = new Client();
$rabbitMQConnection = new AMQPStreamConnection(
    $configuration['parameters']['rabbitMQ']['host'],
    $configuration['parameters']['rabbitMQ']['port'],
    $configuration['parameters']['rabbitMQ']['user'],
    $configuration['parameters']['rabbitMQ']['pass'],
    $configuration['parameters']['rabbitMQ']['vhost']
);
$channel = $rabbitMQConnection->channel();
$channel->exchange_declare(
    $configuration['parameters']['rabbitMQ']['exchange'],
    'topic',
    false,
    true,
    false
);

$app['parameters'] = $configuration['parameters'];
$app['apiClient'] = $guzzleClient;
$app['channel'] = $channel;

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
]);

$app->get('/health/', function () use ($app) {

    if (!isset($app['parameters']['healthy']) || $app['parameters']['healthy'] !== "yes") {
        return new Response('service is not healthy', 500);
    }
    return new Response('service is healthy', 200);
});

$app->get('/', function () use ($app) {

    try {
        $response = ($app['apiClient'])->request('GET', $app['parameters']['dateApiService'], ['timeout' => 2]);
        $content = json_decode($response->getBody()->getContents());
        $date = $content->date;
    } catch (RequestException $e) {
        $event = DateNotAvailableEvent::createWithMessage($e->getMessage());
        $message = new AMQPMessage(json_encode($event), ['content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $app['channel']->basic_publish($message, $app['parameters']['rabbitMQ']['exchange'], 'event.demo.date_not_available');
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
