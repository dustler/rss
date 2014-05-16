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

$echangeName = 'amq.direct';

$channel->exchange_declare($echangeName, 'direct', false, true, false);

/**
 * @var App\Model\Item $item
 */
foreach ($items as $item) {
    $url = $item->getLink();

    $message = array(
        'title' => 'title',
        'timestamp' => time(),
        'payload' => array(
            "url" => $url,
        ),
        'initiator' => 'rssreader',
        'target' => 'evernote',
    );

    $amqpMessage = new \PhpAmqpLib\Message\AMQPMessage(json_encode($message));
    $channel->basic_publish($amqpMessage, $echangeName, 'evernote', true);
    echo "send message\n";
}

$reader->setLastCheckTime(new DateTime('now'));
