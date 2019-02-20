<?php

namespace PiwikBundle\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class RabbitMQProducer
 */
class RabbitMQProducer
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * RabbitMQProducer constructor
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     */
    public function __construct(string $host, string $port, string $user, string $password)
    {
        //$this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        //$this->channel = $this->connection->channel();
    }

    /**
     * @param string $data
     * @param string $queueName
     */
    public function sendEvent(string $data, string $queueName): void
    {
        $message = new AMQPMessage($data);

        //$this->channel->queue_declare($queueName);
        //$this->channel->basic_publish($message);
    }
}