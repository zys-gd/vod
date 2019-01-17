<?php

namespace ExtrasBundle\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Exception\InvalidArgumentException;

class RedisCacheServiceFactory implements ICacheServiceFactory
{
    const PLACEHOLDER_DATABASE = 1;
    const USER_SUBSCRIPTION_DATABASE = 1;

    /**
     * @var RedisConnectionProvider
     */
    private $connectionProvider;
    /**
     * @var string
     */
    private $namespace;

    /**
     * RedisCacheServiceFactory constructor.
     * @param string $host
     * @param string $port
     * @throws \InvalidArgumentException
     */
    public function __construct(RedisConnectionProvider $connectionProvider, string $namespace)
    {


        $this->connectionProvider = $connectionProvider;
        $this->namespace          = $namespace;
    }

    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    public function createPlaceholderCacheService(array $options = []): ICacheService
    {
        return $this->createService(self::PLACEHOLDER_DATABASE, $options, sprintf('%s_%s_', $this->namespace, 'placheolders'));
    }

    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    public function createUserSubscriptionCacheService(array $options = []): ICacheService
    {
        return $this->createService(self::USER_SUBSCRIPTION_DATABASE, $options, sprintf('%s_%s_', $this->namespace, 'subscriptions'));
    }

    /**
     * @param string $database
     * @param array  $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    private function createService(string $database, array $options = [], $namespace = 'default'): ICacheService
    {

        $connection = $this->connectionProvider->create($database, $options);;

        return new RedisCacheService(new RedisAdapter($connection, $namespace));
    }
}
