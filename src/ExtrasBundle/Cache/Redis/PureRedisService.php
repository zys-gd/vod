<?php

namespace ExtrasBundle\Cache\Redis;

use Predis\Client as PureRedis;

/**
 * Class PureRedisService
 */
class PureRedisService
{
    /**
     * @var PureRedis
     */
    private $redis;

    /**
     * PureRedisService constructor
     *
     * @param RedisConnectionProvider $redisConnectionProvider
     */
    public function __construct(RedisConnectionProvider $redisConnectionProvider)
    {
        $this->redis = $redisConnectionProvider->getPureRedisConnection();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return (bool) $this->redis->exists($key);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value)
    {
        $this->redis->set($key, $value);
    }
}