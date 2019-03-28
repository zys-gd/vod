<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.04.18
 * Time: 15:54
 */

namespace ExtrasBundle\Cache;


use Symfony\Component\Cache\Exception\InvalidArgumentException;

class CacheFactoryWrapper implements ICacheServiceFactory
{
    /**
     * @var ICacheServiceFactory
     */
    private $ICacheServiceFactory;

    /**
     * CacheFactoryWrapper constructor.
     * @param ICacheServiceFactory $ICacheServiceFactory
     */
    public function __construct(ICacheServiceFactory $ICacheServiceFactory)
    {
        $this->ICacheServiceFactory = $ICacheServiceFactory;
    }


    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    public function createTranslationCacheService(array $options = []): ICacheService
    {
        return $this->ICacheServiceFactory->createTranslationCacheService($options);
    }

    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    public function createUserSubscriptionCacheService(array $options = []): ICacheService
    {
        return $this->ICacheServiceFactory->createUserSubscriptionCacheService($options);
    }

    /**
     * @param array $options
     * @return ICacheService
     */
    public function createConstraintsByAffiliateCacheService(array $options = []): ICacheService
    {
        return $this->ICacheServiceFactory->createConstraintsByAffiliateCacheService($options);
    }
}