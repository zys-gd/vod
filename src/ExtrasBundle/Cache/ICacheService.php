<?php

namespace ExtrasBundle\Cache;

use Symfony\Component\Cache\CacheItem;

interface ICacheService
{
    /**
     * @param string $key
     * @return CacheItem
     */
    public function getKey(string $key);

    public function saveCache(string $key, $value, int $lifetime);

    public function deleteCache();

    public function hasCache(string $key);

    public function getValue(string $key);
}