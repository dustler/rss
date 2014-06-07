<?php

use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . "/../src/bootstrap.php";

$config = include __DIR__ . "/../config.php";

$amqpConnection = new \PhpAmqpLib\Connection\AMQPConnection(
    $config['amqp']['host'],
    $config['amqp']['port'],
    $config['amqp']['login'],
    $config['amqp']['password']
);
$channel = $amqpConnection->channel();

$exchangeName = 'amq.topic';
$routingKey = "service.rss.*";

$channel->exchange_declare($exchangeName, 'topic', false, true, false);

list($queueName,,) = $channel->queue_declare("service.rss", false, true, false, false);
$channel->queue_bind($queueName, $exchangeName, $routingKey );

$rpc = new App\Rpc\Handler();

$channel->basic_consume($queueName, "service.rss", false, false, false, false, function(\PhpAmqpLib\Message\AMQPMessage $message) use ($rpc, $channel) {

    try {
        echo "message\n";

        if ($message->delivery_info['redelivered']) {
            echo "redelivered\n";
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            return;
        }

        $body = json_decode($message->body, true);

        if (!isset($body['taskId']) || !isset($body['config'])) {
            echo "not valid\n";
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            return;
        }

        $rk = $message->delivery_info['routing_key'];
        $rkParts = explode(".", $rk);

        $method = $rkParts[2];

        echo "Gor message: " . print_r($method, true) . "\n" . print_r($body, true) . "\n";

        $response = call_user_func_array(array($rpc, $method), array($body));
        echo "Response: " . print_r($response);
    }catch (\Exception $e) {
        echo "Exception: " . $e->getMessage() . " " . $e->getFile() . ":" . $e->getLine() . "\n";
        $response = false;
    }

    $answer = array(
        "success" => $response,
    );

    echo "Answer: " . print_r($answer, true) . "\n";

    $msg = new AMQPMessage(
        json_encode($answer),
        array('correlation_id' => mt_rand(0,100000))
    );

    $message->delivery_info['channel']
        ->basic_publish($msg, 'amq.topic', $message->get('reply_to'));
});

while(count($channel->callbacks)) {
    $channel->wait();
}