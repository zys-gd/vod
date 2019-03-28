<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.04.18
 * Time: 16:17
 */

namespace ExtrasBundle\Cache\ArrayCache;


use ExtrasBundle\Cache\ICacheService;
use ExtrasBundle\Cache\ICacheServiceFactory;
use InvalidArgumentException;

class ArrayCacheFactory implements ICacheServiceFactory
{

    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    public function createPlaceholderCacheService(array $options = []): ICacheService
    {
        return new ArrayCacheService();
    }

    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    public function createUserSubscriptionCacheService(array $options = []): ICacheService
    {
        return new ArrayCacheService();
    }

    /**
     * @param array $options
     * @throws \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @return ICacheService
     */
    public function createTranslationCacheService(array $options = []): ICacheService
    {
        return new ArrayCacheService();
    }

    /**
     * @param array $options
     * @return ICacheService
     */
    public function createConstraintsByAffiliateCacheService(array $options = []): ICacheService
    {
        return new ArrayCacheService();
    }
}