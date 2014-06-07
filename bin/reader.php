<?php

error_reporting(E_ALL);

require_once __DIR__ . "/../src/bootstrap.php";

$config = include __DIR__ . "/../config.php";

use App\Reader;

$reader = new Reader();
$entityManager = App\Registry::getEntityManager();

$lastCheckTime = $reader->getLastCheckTime();

$dql = "select i from App\Model\Item i where i.createdAt >= ?1";
$query = $entityManager->createQuery($dql);
$query->setParameter(1, $lastCheckTime);
$items = $query->getResult();

if (empty($items)) {
    $reader->setLastCheckTime(new DateTime('now'));
    return ;
}

$amqpConnection = new \PhpAmqpLib\Connection\AMQPConnection(
    $config['amqp']['host'],
    $config['amqp']['port'],
    $config['amqp']['login'],
    $config['amqp']['password']
);

$channel = $amqpConnection->channel();

$exchangeName = 'amq.topic';

$channel->exchange_declare($exchangeName, 'topic', false, true, false);

/**
 * @var App\Model\Item $item
 */
foreach ($items as $item) {
    $resourceId = $item->getResourceId();
    $resource = $entityManager->find("\\App\\Model\\Resource", $resourceId);
    $tasks = $entityManager->getRepository("\\App\\Model\\Task")->findBy(array(
        "url" => $resource->getLink(),
    ));

    $url = $item->getLink();

    $message = array(
        "url" => $url,
    );

    foreach ($tasks as $task) {
        $message['taskId'] = $task->getTaskId();
        $amqpMessage = new \PhpAmqpLib\Message\AMQPMessage(json_encode($message));
        $channel->basic_publish($amqpMessage, $exchangeName, 'manager.rss', true);
        echo "send message\n";
        echo print_r($message, true) . "\n";
    }
}

$reader->setLastCheckTime(new DateTime('now'));
