<?php

namespace ExtrasBundle\Cache\Redis;

use ExtrasBundle\Cache\ICacheService;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisCacheService implements ICacheService
{
    /** @var RedisAdapter */
    public $adapter;

    /**
     * RedisCacheService constructor.
     * @param RedisAdapter $adapter
     */
    public function __construct(RedisAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get cache item by key
     * @param string $key
     * @return mixed|CacheItemInterface
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getKey(string $key)
    {
        return $this->adapter->getItem($key);
    }

    /**
     * Save item to cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $lifetime
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function saveCache(string $key, $value, int $lifetime)
    {
        $item = $this->adapter->getItem($key);
        $item->set($value);
        $item->expiresAfter($lifetime);
        $this->adapter->save($item);
    }

    /**
     * Flush cache db
     */
    public function deleteCache()
    {
        $this->adapter->clear();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasCache(string $key): bool
    {
        return $this->adapter->hasItem($key);
    }

    /**
     * Get cached item value
     *
     * @param string $key
     * @return mixed
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getValue(string $key)
    {
        if (!$this->adapter->hasItem($key)) {
            return null;
        }

        return $this->adapter->getItem($key)->get();
    }
}
