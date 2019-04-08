<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.05.18
 * Time: 13:27
 */

namespace ExtrasBundle\Cache\Redis;


use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisConnectionProvider
{

    /** @var string */
    private $host;

    /** @var string */
    private $port;

    private $defaultOptions = [
        'timeout'      => 1,
        'read_timeout' => 1,
        'persistent'   => 1,
    ];
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RedisConnectionProvider constructor.
     * @param string $host
     * @param string $port
     * @param string $namespace
     */
    public function __construct(string $host, string $port, string $namespace,  LoggerInterface $logger)
    {
        $this->host      = $host;
        $this->port      = $port;
        $this->namespace = $namespace;
        $this->logger = $logger;
    }

    /**
     * @param       $database
     * @param array $options
     *
     * @return \Predis\Client|\Redis|\RedisCluster
     */
    public function create($database, $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $this->logger->info('Create redis adapter', [
            'databse' => $database,
            'url' => sprintf('redis://%s:%s/' . $database, $this->host, $this->port)
        ]);

        return RedisAdapter::createConnection(
            sprintf('redis://%s:%s/' . $database, $this->host, $this->port),
            $options
        );
    }

    public function createWrapped($database, $options = [])
    {
        $connection = $this->create($database, $options);

        return new RedisAdapter($connection, $this->namespace);
    }
}