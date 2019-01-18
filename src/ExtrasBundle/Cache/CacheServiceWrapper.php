<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.04.18
 * Time: 15:43
 */

namespace ExtrasBundle\Cache;


class CacheServiceWrapper implements ICacheService
{
    /**
     * @var ICacheService
     */
    private $ICacheService;

    /**
     * CacheWrapper constructor.
     * @param ICacheService $ICacheService
     */
    public function __construct(ICacheService $ICacheService)
    {
        $this->ICacheService = $ICacheService;
    }


    public function getKey(string $key)
    {
        return $this->ICacheService->getKey($key);
    }

    public function saveCache(string $key, $value, int $lifetime)
    {
        return $this->ICacheService->saveCache($key, $value, $lifetime);
    }

    public function deleteCache()
    {
        return $this->ICacheService->deleteCache();
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function hasCache(string $key)
    {
        return $this->ICacheService->hasCache($key);
    }

    public function getValue(string $key)
    {
        return $this->ICacheService->getValue($key);
    }
}