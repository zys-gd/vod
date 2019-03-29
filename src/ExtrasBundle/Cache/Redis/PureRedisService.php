<?php

namespace ExtrasBundle\Cache\Redis;

use Predis\Client as PureRedis;
use Predis\ClientInterface;

/**
 * Class PureRedisService
 */
class PureRedisService
{
    /**
     * @var ClientInterface
     */
    private $redis;

    /**
     * PureRedisService constructor.
     *
     * @param ClientInterface $redis
     */
    public function __construct(ClientInterface $redis)
    {
        $this->redis = $redis;
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