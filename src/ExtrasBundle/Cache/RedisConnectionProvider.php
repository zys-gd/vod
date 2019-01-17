<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.05.18
 * Time: 13:27
 */

namespace ExtrasBundle\Cache;


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
     * RedisConnectionProvider constructor.
     * @param string $host
     * @param string $port
     * @param string $namespace
     */
    public function __construct(string $host, string $port, string $namespace)
    {
        $this->host      = $host;
        $this->port      = $port;
        $this->namespace = $namespace;
    }


    public function create($database, $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);

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