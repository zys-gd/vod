<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Locker;


use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\RedisStore;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

class LockerFactory
{
    /**
     * @var \Predis\Client|\Redis|\RedisCluster
     */
    private $redis;

    /**
     * Locker constructor.
     *
     * @param \Predis\Client|\Redis|\RedisCluster $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * @return Factory
     */
    public function createLockFactory()
    {
        $store = new RedisStore($this->redis);
        $store = new RetryTillSaveStore($store);
        return new Factory($store);
    }
}