<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
    }

    public function publishMessage($queueName, $messageBody)
    {
        $channel = $this->connection->channel();
        $channel->exchange_declare('url_events', 'direct');
        $channel->queue_declare($queueName, false, true, false, false);
        $channel->queue_bind($queueName, 'url_events', $queueName);

        $msg = new AMQPMessage($messageBody);
        $channel->basic_publish($msg, '', $queueName);

        $channel->close();
    }
    
    public function __destruct()
    {
        $this->connection->close();
    }
}
