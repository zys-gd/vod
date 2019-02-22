<?php

namespace PiwikBundle\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PiwikBundle\Api\ClientAbstract;

/**
 * Class RabbitMQProducer
 */
class RabbitMQProducer
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $vhost;

    /**
     * @var AMQPStreamConnection
     */
    private $connection = null;

    /**
     * @var AMQPChannel
     */
    private $channel = null;

    /**
     * RabbitMQProducer constructor
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     */
    public function __construct(string $host, string $port, string $user, string $password, string $vhost)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    /**
     * @param string $data
     */
    public function sendEvent(string $data): void
    {
        if (empty($this->connection)) {
            $this->initConnection();
        }

        $message = new AMQPMessage($data);
        $this->channel->basic_publish($message, ClientAbstract::EXCHANGE_NAME);
    }

    /**
     * @return \Closure
     */
    private function shutdown(): \Closure
    {
        return function() {
            $this->channel->close();
            $this->connection->close();
        };
    }

    /**
     * @return void
     */
    private function initConnection(): void
    {
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
        $this->channel = $this->connection->channel();

        $this->channel->queue_declare(ClientAbstract::QUEUE_NAME, false, true, false, false);
        $this->channel->exchange_declare(ClientAbstract::EXCHANGE_NAME, 'direct', false, true, false);
        $this->channel->queue_bind(ClientAbstract::QUEUE_NAME, ClientAbstract::EXCHANGE_NAME);

        register_shutdown_function($this->shutdown());
    }
}