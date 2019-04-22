<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Locker;


use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterDataConverter;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Lock;
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

    /**
     * @return Lock
     */
    public function lock(): Lock
    {
        $lockerFactory = $this->createLockFactory();
        $lock          = $lockerFactory->createLock(LimiterDataConverter::KEY, 2);
        // $lock->acquire();

        return $lock;
    }

    /**
     * @param Lock $lock
     */
    public function unlock(Lock $lock)
    {
        $lock->release();
    }
}