<?php

require_once __DIR__ . "/../src/bootstrap.php";

$config = include __DIR__ . "/../config.php";

$amqpConnection = new \PhpAmqpLib\Connection\AMQPConnection(
    $config['amqp']['host'],
    $config['amqp']['port'],
    $config['amqp']['login'],
    $config['amqp']['password']
);
$channel = $amqpConnection->channel();

$exchangeName = 'amq.direct';

$channel->exchange_declare($exchangeName, 'direct', false, true, false);

list($queueName,,) = $channel->queue_declare("", false, true, false, true);
$channel->queue_bind($queueName, $exchangeName, 'rssreader.api');

$reader = new App\Reader();

$channel->basic_consume($queueName, "rssreader", false, false, false, false, function(\PhpAmqpLib\Message\AMQPMessage $message) use ($reader) {
    if ($message->delivery_info['redelivered']) {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        return;
    }

    $body = json_decode($message->body, true);
    $properties = $message->get_properties();
    $headers = $properties["application_headers"];

    if (!isset($headers['method'])) {
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
        return;
    }

    $method = $headers['method'][1];

    echo "Gor message: " . print_r($method, true) . "\n" . print_r($body, true) . "\n";

    if ($method == 'addFeed' && $body != false) {
        $url = $body[0];
        $res = call_user_func_array(array($reader, 'addFeed'), $body);
        if ($res) {
            echo "Feed " . $url . " added\n";
        } else {
            echo "Feed " . $url . " not added\n";
        }
    }
});

while(count($channel->callbacks)) {
    $channel->wait();
}