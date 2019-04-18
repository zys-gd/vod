<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Locker\LockerFactory;
use Symfony\Component\Lock\Lock;

class LimiterPerformer
{
    /**
     * @var \Predis\Client|\Redis|\RedisCluster
     */
    private $redis;
    /**
     * @var LimiterDataMapper
     */
    private $limiterDataMapper;
    /**
     * @var LockerFactory
     */
    private $lockerFactory;

    public function __construct($redis, LimiterDataMapper $limiterDataMapper, LockerFactory $lockerFactory)
    {
        $this->redis             = $redis;
        $this->limiterDataMapper = $limiterDataMapper;
        $this->lockerFactory     = $lockerFactory;
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function saveCarrierConstraint(CarrierLimiterData $carrierLimiterData): array
    {
        $data = $this->limiterDataMapper->convertCarrierLimiterData2Array($carrierLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function saveCarrierAffiliateConstraint(AffiliateLimiterData $affiliateLimiterData): array
    {
        $data = $this->limiterDataMapper->convertCarrierAffiliateConstraint($affiliateLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param int $billingCarrierId
     *
     * @return array
     */
    public function getCarrierSlots(int $billingCarrierId): array
    {
        $data = $this->limiterDataMapper->extractCarrierSlots($this->getDataFromRedisAsArray(), $billingCarrierId);

        return $data;
    }

    /**
     * @param int    $billingCarrierId
     * @param string $affiliateUuid
     * @param string $constraintUuid
     *
     * @return array
     */
    public function getCarrierAffiliateConstraintSlots(int $billingCarrierId,
        string $affiliateUuid,
        string $constraintUuid): array
    {
        $data = $this->limiterDataMapper->extractAffiliateSlots($this->getDataFromRedisAsArray(), $billingCarrierId, $affiliateUuid, $constraintUuid);

        return $data;
    }

    /**
     * @param int    $billingCarrierId
     * @param string $affiliateUuid
     * @param string $constraintUuid
     */
    public function removeAffiliateConstraint(int $billingCarrierId, string $affiliateUuid, string $constraintUuid)
    {
        try {
            $data = $this->getDataFromRedisAsArray();
            unset($data[LimiterDataMapper::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid]);
            $this->redis->set(LimiterDataMapper::KEY, json_encode($data));
        } catch (\Throwable $e) {
            echo "Cant remove from redis, reason: " . $e->getMessage();
        }
    }

    /**
     * @param int $billingCarrierId
     */
    public function removeCarrierConstraint(int $billingCarrierId)
    {
        try {
            $data = $this->getDataFromRedisAsArray();
            unset($data[LimiterDataMapper::KEY][$billingCarrierId][LimiterDataMapper::PROCESSING_SLOTS]);
            unset($data[LimiterDataMapper::KEY][$billingCarrierId][LimiterDataMapper::OPEN_SUBSCRIPTION_SLOTS]);
            $this->redis->set(LimiterDataMapper::KEY, json_encode($data));
        } catch (\Throwable $e) {
            echo "Cant remove from redis, reason: " . $e->getMessage();
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function set2Storage(array $data): array
    {
        if ($this->redis->exists(LimiterDataMapper::KEY)) {
            $data = array_replace_recursive(
                $this->getDataFromRedisAsArray(),
                $data
            );
        }
        $this->redis->set(LimiterDataMapper::KEY, json_encode($data));
        return $data;
    }

    /**
     * @return array
     */
    private function getDataFromRedisAsArray(): array
    {
        return json_decode($this->redis->get(LimiterDataMapper::KEY), true) ?? [];
    }

    /**
     * @param int $billingCarrierId
     */
    public function decrCarrierProcessingSlotsWithLock(int $billingCarrierId)
    {
        $lock = $this->lock();

        try {
            $redisData = $this->getDataFromRedisAsArray();
            $slots     = $this->limiterDataMapper->extractCarrierSlots($redisData, $billingCarrierId);

            if ($slots[LimiterDataMapper::PROCESSING_SLOTS]-- >= 0) {
                $data = $this->limiterDataMapper->updateCarrierSlots($billingCarrierId, $slots);
                $this->set2Storage($data);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    /**
     * @param int    $billingCarrierId
     * @param string $affiliateUuid
     * @param string $constraintUuid
     */
    public function decrAffiliateProcessingSlotsWithLock(int $billingCarrierId,
        string $affiliateUuid,
        string $constraintUuid)
    {
        $lock = $this->lock();

        try {
            $redisData = $this->getDataFromRedisAsArray();
            $slots     = $this->limiterDataMapper->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);

            if ($slots[LimiterDataMapper::PROCESSING_SLOTS]-- >= 0) {
                $data = $this->limiterDataMapper->updateCarrierSlots($billingCarrierId, $slots);
                $this->set2Storage($data);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    /**
     * @param int    $billingCarrierId
     * @param string $affiliateUuid
     * @param string $constraintUuid
     */
    public function incrAffiliateProcessingSlotsWithLock(int $billingCarrierId,
        string $affiliateUuid,
        string $constraintUuid)
    {
        $lock = $this->lock();

        try {
            $redisData = $this->getDataFromRedisAsArray();
            $slots     = $this->limiterDataMapper->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);
            $slots[LimiterDataMapper::PROCESSING_SLOTS]++;
            $data = $this->limiterDataMapper->updateCarrierSlots($billingCarrierId, $slots);

            $this->set2Storage($data);
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    /**
     * @param int $billingCarrierId
     */
    public function incrCarrierProcessingSlotsWithLock(int $billingCarrierId)
    {
        $lock = $this->lock();

        try {
            $redisData = $this->getDataFromRedisAsArray();
            $slots     = $this->limiterDataMapper->extractCarrierSlots($redisData, $billingCarrierId);
            $slots[LimiterDataMapper::PROCESSING_SLOTS]++;
            $data = $this->limiterDataMapper->updateCarrierSlots($billingCarrierId, $slots);

            $this->set2Storage($data);
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    /**
     * @param int    $billingCarrierId
     * @param string $affiliateUuid
     * @param string $constraintUuid
     */
    public function decrAffiliateSubscriptionSlotsWithLock(int $billingCarrierId,
        string $affiliateUuid,
        string $constraintUuid)
    {
        $lock = $this->lock();

        try {
            $redisData = $this->getDataFromRedisAsArray();
            $slots     = $this->limiterDataMapper->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);

            if ($slots[LimiterDataMapper::OPEN_SUBSCRIPTION_SLOTS]-- >= 0) {
                $data = $this->limiterDataMapper->updateCarrierSlots($billingCarrierId, $slots);
                $this->set2Storage($data);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    /**
     * @param int $billingCarrierId
     */
    public function decrCarrierSubscriptionSlotsWithLock(int $billingCarrierId)
    {
        $lock = $this->lock();

        try {
            $redisData = $this->getDataFromRedisAsArray();
            $slots     = $this->limiterDataMapper->extractCarrierSlots($redisData, $billingCarrierId);

            if ($slots[LimiterDataMapper::OPEN_SUBSCRIPTION_SLOTS]-- >= 0) {
                $data = $this->limiterDataMapper->updateCarrierSlots($billingCarrierId, $slots);
                $this->set2Storage($data);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    /**
     * @return Lock
     */
    private function lock(): Lock
    {
        $lockerFactory = $this->lockerFactory->createLockFactory();
        $lock          = $lockerFactory->createLock(LimiterDataMapper::KEY, 2);
        // $lock->acquire();

        return $lock;
    }

    /**
     * @param Lock $lock
     */
    private function unlock(Lock $lock)
    {
        $lock->release();
    }
}