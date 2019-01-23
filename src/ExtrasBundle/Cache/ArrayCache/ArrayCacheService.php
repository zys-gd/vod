<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.04.18
 * Time: 15:51
 */

namespace ExtrasBundle\Cache\ArrayCache;


use ExtrasBundle\Cache\ICacheService;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class ArrayCacheService implements ICacheService
{
    private $adapter;

    public function __construct()
    {
        $this->adapter = new ArrayAdapter();
    }

    /**
     * Get cache item by key
     * @param string $key
     * @return CacheItemInterface
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
     * @param mixed  $value
     * @param int    $lifetime
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
     * @throws \Psr\Cache\InvalidArgumentException
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