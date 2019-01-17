<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.04.18
 * Time: 15:55
 */

namespace ExtrasBundle\Cache;

use Symfony\Component\Cache\Exception\InvalidArgumentException;

interface ICacheServiceFactory
{
    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    public function createPlaceholderCacheService(array $options = []): ICacheService;

    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return ICacheService
     */
    public function createUserSubscriptionCacheService(array $options = []): ICacheService;
}