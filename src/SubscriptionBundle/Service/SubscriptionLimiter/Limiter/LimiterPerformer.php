<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\LimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Locker\LockerFactory;
use Symfony\Component\Lock\Lock;

class LimiterPerformer/* implements LimiterInterface*/
{
    /**
     * @var \Predis\Client|\Redis|\RedisCluster
     */
    private $redis;
    /**
     * @var LimiterStructureGear
     */
    private $limiterStructureGear;
    /**
     * @var LockerFactory
     */
    private $lockerFactory;

    public function __construct($redis, LimiterStructureGear $limiterStructureGear, LockerFactory $lockerFactory)
    {
        $this->redis                = $redis;
        $this->limiterStructureGear = $limiterStructureGear;
        $this->lockerFactory        = $lockerFactory;
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function saveCarrierConstraint(LimiterData $limiterData): array
    {
        $data = $this->limiterStructureGear->saveCarrierConstraint($limiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function saveCarrierAffiliateConstraint(LimiterData $limiterData): array
    {
        $data = $this->limiterStructureGear->saveCarrierAffiliateConstraint($limiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function updateCarrierSlots(LimiterData $limiterData): array
    {
        $data = $this->limiterStructureGear->updateCarrierSlots($limiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function updateCarrierAffiliateConstraintSlots(LimiterData $limiterData): array
    {
        $data = $this->limiterStructureGear->updateCarrierAffiliateConstraintSlots($limiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function getCarrierSlots(LimiterData $limiterData): array
    {
        $data = $this->limiterStructureGear->getCarrierSlots($this->getDataFromRedisAsArray(), $limiterData);

        return $data;
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function getCarrierAffiliateConstraintSlots(LimiterData $limiterData): array
    {
        $data = $this->limiterStructureGear->getCarrierAffiliateConstraintSlots($this->getDataFromRedisAsArray(), $limiterData);

        return $data;
    }

    public function removeAffiliateConstraint(LimiterData $limiterData)
    {
        try{
            $data = $this->getDataFromRedisAsArray();
            unset($data[LimiterStructureGear::KEY][$limiterData->getCarrier()->getBillingCarrierId()][$limiterData->getAffiliate()->getUuid()][$limiterData->getSubscriptionConstraint()->getUuid()]);
            $this->redis->set(LimiterStructureGear::KEY, json_encode($data));
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
        if ($this->redis->exists(LimiterStructureGear::KEY)) {
            $data = array_replace_recursive(
                $this->getDataFromRedisAsArray(),
                $data
            );
        }
        $this->redis->set(LimiterStructureGear::KEY, json_encode($data));
        return $data;
    }

    private function getDataFromRedisAsArray(): array
    {
        return json_decode($this->redis->get(LimiterStructureGear::KEY), true) ?? [];
    }

    public function decrCarrierProcessingSlotsWithLock(LimiterData $limiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierSlots($limiterData);
            if ($slots[LimiterStructureGear::PROCESSING_SLOTS] > 0) {
                $limiterData->setCarrierProcessingSlots($slots[LimiterStructureGear::PROCESSING_SLOTS] - 1);
                $this->updateCarrierSlots($limiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function decrAffiliateProcessingSlotsWithLock(LimiterData $limiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierAffiliateConstraintSlots($limiterData);
            if ($slots[LimiterStructureGear::PROCESSING_SLOTS] > 0) {
                $limiterData->setAffiliateProcessingSlots($slots[LimiterStructureGear::PROCESSING_SLOTS] - 1);
                $this->updateCarrierAffiliateConstraintSlots($limiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function incrAffiliateProcessingSlotsWithLock(LimiterData $limiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierAffiliateConstraintSlots($limiterData);
            $limiterData->setAffiliateProcessingSlots($slots[LimiterStructureGear::PROCESSING_SLOTS] + 1);
            $this->updateCarrierAffiliateConstraintSlots($limiterData);
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function incrCarrierProcessingSlotsWithLock(LimiterData $limiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierSlots($limiterData);
            $limiterData->setCarrierProcessingSlots($slots[LimiterStructureGear::PROCESSING_SLOTS] + 1);
            $this->updateCarrierSlots($limiterData);
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function decrAffiliateSubscriptionSlotsWithLock(LimiterData $limiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierAffiliateConstraintSlots($limiterData);
            if ($slots[LimiterStructureGear::OPEN_SUBSCRIPTION_SLOTS] > 0) {
                $limiterData->setAffiliateOpenSubscriptionSlots($slots[LimiterStructureGear::OPEN_SUBSCRIPTION_SLOTS] - 1);
                $this->updateCarrierAffiliateConstraintSlots($limiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $this->unlock($lock);
        }
    }

    public function decrCarrierSubscriptionSlotsWithLock(LimiterData $limiterData)
    {
        $lock = $this->lock();

        try {
            $slots = $this->getCarrierSlots($limiterData);
            if ($slots[LimiterStructureGear::OPEN_SUBSCRIPTION_SLOTS] > 0) {
                $limiterData->setCarrierOpenSubscriptionSlots($slots[LimiterStructureGear::OPEN_SUBSCRIPTION_SLOTS] - 1);
                $this->updateCarrierSlots($limiterData);
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
        $lock          = $lockerFactory->createLock(LimiterStructureGear::KEY, 2);
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