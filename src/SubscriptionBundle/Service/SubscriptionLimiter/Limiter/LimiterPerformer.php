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
    /**
     * @var LimiterDataExtractor
     */
    private $limiterDataExtractor;

    /**
     * LimiterPerformer constructor.
     *
     * @param                      $redis
     * @param LimiterDataConverter $limiterDataConverter
     * @param LockerFactory        $lockerFactory
     * @param LimiterDataExtractor $limiterDataExtractor
     */
    public function __construct($redis,
        LimiterDataConverter $limiterDataConverter,
        LockerFactory $lockerFactory,
        LimiterDataExtractor $limiterDataExtractor)
    {
        $this->redis                = $redis;
        $this->limiterDataConverter = $limiterDataConverter;
        $this->lockerFactory        = $lockerFactory;
        $this->limiterDataExtractor = $limiterDataExtractor;
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
    public function getDataFromRedisAsArray(): array
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
            $slots     = $this->limiterDataExtractor->extractCarrierSlots($redisData, $billingCarrierId);

            if ($slots[LimiterDataConverter::PROCESSING_SLOTS]-- >= 0) {
                $data = $this->limiterDataConverter->convertCarrierSlots2Array($billingCarrierId, $slots);
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
            $slots     = $this->limiterDataExtractor->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);

            if ($slots[LimiterDataConverter::PROCESSING_SLOTS]-- >= 0) {
                $data = $this->limiterDataConverter->convertCarrierAffiliateConstraintSlots2Array($billingCarrierId, $affiliateUuid, $constraintUuid, $slots);
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
            $slots     = $this->limiterDataExtractor->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);
            $slots[LimiterDataConverter::PROCESSING_SLOTS]++;
            $data = $this->limiterDataConverter->convertCarrierSlots2Array($billingCarrierId, $slots);

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
            $slots     = $this->limiterDataExtractor->extractCarrierSlots($redisData, $billingCarrierId);
            $slots[LimiterDataConverter::PROCESSING_SLOTS]++;
            $data = $this->limiterDataConverter->convertCarrierSlots2Array($billingCarrierId, $slots);

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
            $slots     = $this->limiterDataExtractor->extractAffiliateSlots($redisData, $billingCarrierId, $affiliateUuid, $constraintUuid);

            if ($slots[LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS]-- >= 0) {
                $data = $this->limiterDataConverter->convertCarrierSlots2Array($billingCarrierId, $slots);
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
            $slots     = $this->limiterDataExtractor->extractCarrierSlots($redisData, $billingCarrierId);

            if ($slots[LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS]-- >= 0) {
                $data = $this->limiterDataConverter->convertCarrierSlots2Array($billingCarrierId, $slots);
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