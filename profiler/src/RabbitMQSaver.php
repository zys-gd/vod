<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.07.18
 * Time: 15:54
 */

class RabbitMQSaver implements Xhgui_Saver_Interface
{
    private $connection;


    const DEFAULT_EXCHANGE_NAME = 'profiler_exchange';
    /**
     * @var string
     */
    private $queueName;

    /**
     * RabbitMQSaver constructor.
     */
    public function __construct(string $host, int $port = 5672, string $login = null, string $password = null, string $vhost = '/', string $queueName = 'profiler_logs')
    {
        $this->connection = new \AMQPConnection([
            'host'     => $host,
            'port'     => $port,
            'login'    => $login,
            'password' => $password,
            'vhost'    => $vhost
        ]);
        $this->queueName  = $queueName;
    }

    public function save(array $data)
    {
        if (!$this->connection->isConnected()) {
            $this->connection->pconnect();
        }

        $channel  = new \AMQPChannel($this->connection);
        $exchange = new \AMQPExchange($channel);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->setName(self::DEFAULT_EXCHANGE_NAME);
        $exchange->setType('direct');
        $exchange->declareExchange();

        $queue = new \AMQPQueue($channel);
        $queue->setName($this->queueName);
        $queue->setFlags(AMQP_DURABLE | AMQP_AUTODELETE);
        $queue->declareQueue();
        $queue->bind(self::DEFAULT_EXCHANGE_NAME, $this->queueName);

        $exchange->publish(
            json_encode($data),
            $this->queueName,
            AMQP_MANDATORY,
            [
                'content_type'  => 'application/json',
                'delivery_mode' => 2
            ]
        );
    }

}