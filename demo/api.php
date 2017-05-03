<?php

namespace Mijndomein\Demo;

use DateTimeZone;
use Mijndomein\Demo\Events\DateTimeEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__.'/../vendor/autoload.php';

$configuration = Yaml::parse(
    file_get_contents(__DIR__ . "/config/parameters.yml")
);

$app = new Application();
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
$app['channel'] = $channel;

$app->get('/health/', function () use ($app) {
    return new Response('service is healthy', 200);
});

$app->get('/', function () use ($app) {
    $dateTime = new \DateTime('now', new DateTimeZone('Europe/Amsterdam'));
    try {
        $event = DateTimeEvent::create($dateTime);
        $message = new AMQPMessage(json_encode($event), ['content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $app['channel']->basic_publish($message, $app['parameters']['rabbitMQ']['exchange'], 'event.demo-api.date_time_now');
    } catch (\Exception $e) {
        //ignore
    }
    return new JsonResponse(['date' => $dateTime->format('d-m-Y')]);
});

$app->run();
