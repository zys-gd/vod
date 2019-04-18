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
     * @var LimiterDataConverter
     */
    private $limiterDataConverter;
    /**
     * @var LockerFactory
     */
    private $lockerFactory;

    public function __construct($redis, LimiterDataConverter $limiterDataConverter, LockerFactory $lockerFactory)
    {
        $this->redis                = $redis;
        $this->limiterDataConverter = $limiterDataConverter;
        $this->lockerFactory        = $lockerFactory;
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function saveCarrierConstraint(CarrierLimiterData $carrierLimiterData): array
    {
        $data = $this->limiterDataConverter->convertCarrierLimiterData2Array($carrierLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function saveCarrierAffiliateConstraint(AffiliateLimiterData $affiliateLimiterData): array
    {
        $data = $this->limiterDataConverter->convertCarrierAffiliateConstraint($affiliateLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param int $billingCarrierId
     *
     * @return array
     */
    public function getCarrierSlots(int $billingCarrierId): array
    {
        $data = $this->limiterDataConverter->extractCarrierSlots($this->getDataFromRedisAsArray(), $billingCarrierId);

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
        $data = $this->limiterDataConverter->extractAffiliateSlots($this->getDataFromRedisAsArray(), $billingCarrierId, $affiliateUuid, $constraintUuid);

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
            unset($data[LimiterDataConverter::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid]);
            $this->redis->set(LimiterDataConverter::KEY, json_encode($data));
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
            unset($data[LimiterDataConverter::KEY][$billingCarrierId][LimiterDataConverter::PROCESSING_SLOTS]);
            unset($data[LimiterDataConverter::KEY][$billingCarrierId][LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS]);
            $this->redis->set(LimiterDataConverter::KEY, json_encode($data));
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
        if ($this->redis->exists(LimiterDataConverter::KEY)) {
            $data = array_replace_recursive(
                $this->getDataFromRedisAsArray(),
                $data
            );
        }
        $this->redis->set(LimiterDataConverter::KEY, json_encode($data));
        return $data;
    }

    /**
     * @return array
     */
    private function getDataFromRedisAsArray(): array
    {
        return json_decode($this->redis->get(LimiterDataConverter::KEY), true) ?? [];
    }

    /**
     * @param int $billingCarrierId
     */
    public function decrCarrierProcessingSlotsWithLock(int $billingCarrierId)
    {
        $lock = $this->lock();

        try {
            $redisData = $this->getDataFromRedisAsArray();
            $slots     = $this->limiterDataConverter->extractCarrierSlots($redisData, $billingCarrierId);

            if ($slots[LimiterDataConverter::PROCESSING_SLOTS]-- >= 0) {
                $data = $this->limiterDataConverter->updateCarrierSlots($billingCarrierId, $slots);
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
            $slots     = $this->limiterDataConverter->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);

            if ($slots[LimiterDataConverter::PROCESSING_SLOTS]-- >= 0) {
                $data = $this->limiterDataConverter->updateCarrierAffiliateConstraintSlots($billingCarrierId,  $affiliateUuid, $constraintUuid, $slots);
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
            $slots     = $this->limiterDataConverter->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);
            $slots[LimiterDataConverter::PROCESSING_SLOTS]++;
            $data = $this->limiterDataConverter->updateCarrierSlots($billingCarrierId, $slots);

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
            $slots     = $this->limiterDataConverter->extractCarrierSlots($redisData, $billingCarrierId);
            $slots[LimiterDataConverter::PROCESSING_SLOTS]++;
            $data = $this->limiterDataConverter->updateCarrierSlots($billingCarrierId, $slots);

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
            $slots     = $this->limiterDataConverter->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);

            if ($slots[LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS]-- >= 0) {
                $data = $this->limiterDataConverter->updateCarrierSlots($billingCarrierId, $slots);
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
            $slots     = $this->limiterDataConverter->extractCarrierSlots($redisData, $billingCarrierId);

            if ($slots[LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS]-- >= 0) {
                $data = $this->limiterDataConverter->updateCarrierSlots($billingCarrierId, $slots);
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
        $lock          = $lockerFactory->createLock(LimiterDataConverter::KEY, 2);
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