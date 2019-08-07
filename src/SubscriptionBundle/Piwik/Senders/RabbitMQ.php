<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.08.19
 * Time: 12:32
 */

namespace SubscriptionBundle\Piwik\Senders;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements SenderInterface
{
    const EXCHANGE_NAME = 'piwik-vod';
    const QUEUE_NAME = 'piwik-events-vod';
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
     * @var string
     */
    private $exchangeName;
    /**
     * @var string
     */
    private $queueName;

    /**
     * RabbitMQProducer constructor
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     * @param string $exchangeName
     * @param string $queueName
     */
    public function __construct(string $host, string $port, string $user, string $password, string $vhost, string $exchangeName, string $queueName)
    {
        $this->host         = $host;
        $this->port         = $port;
        $this->user         = $user;
        $this->password     = $password;
        $this->vhost        = $vhost;
        $this->exchangeName = $exchangeName;
        $this->queueName    = $queueName;
    }

    /**
     * @param array  $data
     * @param string $timestamp
     * @return bool
     */
    public function sendEvent($data, string $timestamp): bool
    {
        if (empty($this->connection)) {
            $this->initConnection();
        }

        $preparedData = [$data, $timestamp];

        $message = new AMQPMessage(json_encode($preparedData));
        $this->channel->basic_publish($message, $this->exchangeName);

        return true;
    }

    /**
     * @return \Closure
     */
    private function shutdown(): \Closure
    {
        return function () {
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
        $this->channel    = $this->connection->channel();

        $this->channel->queue_declare($this->queueName, false, true, false, false);
        $this->channel->exchange_declare($this->exchangeName, 'direct', false, true, false);
        $this->channel->queue_bind($this->queueName, $this->exchangeName);

        register_shutdown_function($this->shutdown());
    }
}