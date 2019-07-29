<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.07.19
 * Time: 14:18
 */

namespace App\Cache;


use ExtrasBundle\Cache\CacheFactoryWrapper;
use ExtrasBundle\Cache\ICacheService;

class ConfiguredCacheFactoryWrapper extends CacheFactoryWrapper
{
    /**
     * @var CacheFactoryWrapper
     */
    private $cacheFactoryWrapper;


    /**
     * ConfiguredCacheFactoryWrapper constructor.
     * @param CacheFactoryWrapper $cacheFactoryWrapper
     */
    public function __construct(CacheFactoryWrapper $cacheFactoryWrapper)
    {
        $this->cacheFactoryWrapper = $cacheFactoryWrapper;
    }

    public function createCacheService(int $database, string $namespace, array $options = []): ICacheService
    {

        $defaults = [
            'class'        => 'Redis',
            'read_timeout' => '1',
            'timeout'      => '1',
            'persistent'   => true,
            'lazy'         => true
        ];

        return $this->cacheFactoryWrapper->createCacheService($database, $namespace, array_merge($defaults, $options));
    }


}