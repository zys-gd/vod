<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Locker\LockerFactory;
use Symfony\Component\Lock\Lock;

class LimiterPerformer/* implements LimiterInterface*/
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
        $data = $this->limiterDataMapper->saveCarrierConstraint($carrierLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function saveCarrierAffiliateConstraint(AffiliateLimiterData $affiliateLimiterData): array
    {
        $data = $this->limiterDataMapper->saveCarrierAffiliateConstraint($affiliateLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function updateCarrierSlots(CarrierLimiterData $carrierLimiterData): array
    {
        $data = $this->limiterDataMapper->updateCarrierSlots($carrierLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function updateCarrierAffiliateConstraintSlots(AffiliateLimiterData $affiliateLimiterData): array
    {
        $data = $this->limiterDataMapper->updateCarrierAffiliateConstraintSlots($affiliateLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function getCarrierSlots(CarrierLimiterData $carrierLimiterData): array
    {
        $data = $this->limiterDataMapper->getCarrierSlots($this->getDataFromRedisAsArray(), $carrierLimiterData);

        return $data;
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function getCarrierAffiliateConstraintSlots(AffiliateLimiterData $affiliateLimiterData): array
    {
        $data = $this->limiterDataMapper->getCarrierAffiliateConstraintSlots($this->getDataFromRedisAsArray(), $affiliateLimiterData);

        return $data;
    }

    public function removeAffiliateConstraint(AffiliateLimiterData $affiliateLimiterData)
    {
        try {
            $data = $this->getDataFromRedisAsArray();
            unset($data[LimiterDataMapper::KEY][$affiliateLimiterData->getBillingCarrierId()][$affiliateLimiterData->getAffiliate()->getUuid()][$affiliateLimiterData->getConstraintByAffiliate()->getUuid()]);
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

    private function getDataFromRedisAsArray(): array
    {
        return json_decode($this->redis->get(LimiterDataMapper::KEY), true) ?? [];
    }

    public function decrCarrierProcessingSlotsWithLock(CarrierLimiterData $carrierLimiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierSlots($carrierLimiterData);
            if ($slots[LimiterDataMapper::PROCESSING_SLOTS] > 0) {
                $carrierLimiterData->setCarrierProcessingSlots($slots[LimiterDataMapper::PROCESSING_SLOTS] - 1);
                $this->updateCarrierSlots($carrierLimiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function decrAffiliateProcessingSlotsWithLock(AffiliateLimiterData $affiliateLimiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierAffiliateConstraintSlots($affiliateLimiterData);
            if ($slots[LimiterDataMapper::PROCESSING_SLOTS] > 0) {
                $affiliateLimiterData->setAffiliateProcessingSlots($slots[LimiterDataMapper::PROCESSING_SLOTS] - 1);
                $this->updateCarrierAffiliateConstraintSlots($affiliateLimiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function incrAffiliateProcessingSlotsWithLock(AffiliateLimiterData $affiliateLimiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierAffiliateConstraintSlots($affiliateLimiterData);
            $affiliateLimiterData->setAffiliateProcessingSlots($slots[LimiterDataMapper::PROCESSING_SLOTS] + 1);
            $this->updateCarrierAffiliateConstraintSlots($affiliateLimiterData);
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function incrCarrierProcessingSlotsWithLock(CarrierLimiterData $carrierLimiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierSlots($carrierLimiterData);
            $carrierLimiterData->setCarrierProcessingSlots($slots[LimiterDataMapper::PROCESSING_SLOTS] + 1);
            $this->updateCarrierSlots($carrierLimiterData);
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function decrAffiliateSubscriptionSlotsWithLock(?AffiliateLimiterData $affiliateLimiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierAffiliateConstraintSlots($affiliateLimiterData);
            if ($slots[LimiterDataMapper::OPEN_SUBSCRIPTION_SLOTS] > 0) {
                $affiliateLimiterData->setAffiliateOpenSubscriptionSlots($slots[LimiterDataMapper::OPEN_SUBSCRIPTION_SLOTS] - 1);
                $this->updateCarrierAffiliateConstraintSlots($affiliateLimiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function decrCarrierSubscriptionSlotsWithLock(CarrierLimiterData $carrierLimiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierSlots($carrierLimiterData);
            if ($slots[LimiterDataMapper::OPEN_SUBSCRIPTION_SLOTS] > 0) {
                $carrierLimiterData->setCarrierOpenSubscriptionSlots($slots[LimiterDataMapper::OPEN_SUBSCRIPTION_SLOTS] - 1);
                $this->updateCarrierSlots($carrierLimiterData);
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