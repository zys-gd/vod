<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\LimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Locker\LockerFactory;

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
    public function setCarrierConstraint(LimiterData $limiterData): array
    {
        $data = $this->limiterStructureGear->setCarrierConstraint($limiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function setCarrierAffiliateConstraint(LimiterData $limiterData): array
    {
        $data = $this->limiterStructureGear->setCarrierAffiliateConstraint($limiterData);

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
        $lockerFactory = $this->lockerFactory->createLockFactory();
        $lock          = $lockerFactory->createLock(LimiterStructureGear::KEY);
        $lock->acquire();

        try{
            $slots = $this->getCarrierSlots($limiterData);
            if ($slots[LimiterStructureGear::PROCESSING_SLOTS] > 0) {
                $limiterData->setCarrierProcessingSlots($slots[LimiterStructureGear::PROCESSING_SLOTS]-1);
                $this->updateCarrierSlots($limiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $lock->release();
        }
    }

    public function decrAffiliateProcessingSlotsWithLock(LimiterData $limiterData)
    {
        $lockerFactory = $this->lockerFactory->createLockFactory();
        $lock          = $lockerFactory->createLock(LimiterStructureGear::KEY);
        $lock->acquire();

        try{
            $slots = $this->getCarrierAffiliateConstraintSlots($limiterData);
            if ($slots[LimiterStructureGear::PROCESSING_SLOTS] > 0) {
                $limiterData->setAffiliateProcessingSlots($slots[LimiterStructureGear::PROCESSING_SLOTS]-1);
                $this->updateCarrierAffiliateConstraintSlots($limiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $lock->release();
        }
    }

    public function incrAffiliateProcessingSlotsWithLock(LimiterData $limiterData)
    {
        $lockerFactory = $this->lockerFactory->createLockFactory();
        $lock          = $lockerFactory->createLock(LimiterStructureGear::KEY);
        $lock->acquire();

        try{
            $slots = $this->getCarrierAffiliateConstraintSlots($limiterData);
            $limiterData->setAffiliateProcessingSlots($slots[LimiterStructureGear::PROCESSING_SLOTS]+1);
            $this->updateCarrierAffiliateConstraintSlots($limiterData);
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $lock->release();
        }
    }

    public function incrCarrierProcessingSlotsWithLock(LimiterData $limiterData)
    {
        $lockerFactory = $this->lockerFactory->createLockFactory();
        $lock          = $lockerFactory->createLock(LimiterStructureGear::KEY);
        $lock->acquire();

        try{
            $slots = $this->getCarrierSlots($limiterData);
            $limiterData->setCarrierProcessingSlots($slots[LimiterStructureGear::PROCESSING_SLOTS]+1);
            $this->updateCarrierSlots($limiterData);
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $lock->release();
        }
    }

    public function decrAffiliateSubscriptionSlotsWithLock(LimiterData $limiterData)
    {
        $lockerFactory = $this->lockerFactory->createLockFactory();
        $lock          = $lockerFactory->createLock(LimiterStructureGear::KEY);
        $lock->acquire();

        try{
            $slots = $this->getCarrierAffiliateConstraintSlots($limiterData);
            if ($slots[LimiterStructureGear::OPEN_SUBSCRIPTION_SLOTS] > 0) {
                $limiterData->setAffiliateOpenSubscriptionSlots($slots[LimiterStructureGear::OPEN_SUBSCRIPTION_SLOTS]-1);
                $this->updateCarrierAffiliateConstraintSlots($limiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $lock->release();
        }
    }

    public function decrCarrierSubscriptionSlotsWithLock(LimiterData $limiterData)
    {
        $lockerFactory = $this->lockerFactory->createLockFactory();
        $lock          = $lockerFactory->createLock(LimiterStructureGear::KEY);
        $lock->acquire();

        try{
            $slots = $this->getCarrierSlots($limiterData);
            if ($slots[LimiterStructureGear::OPEN_SUBSCRIPTION_SLOTS] > 0) {
                $limiterData->setCarrierOpenSubscriptionSlots($slots[LimiterStructureGear::OPEN_SUBSCRIPTION_SLOTS]-1);
                $this->updateCarrierSlots($limiterData);
            }
        } catch (\Throwable $e) {
            // smth throw
        } finally {
            $lock->release();
        }
    }
}