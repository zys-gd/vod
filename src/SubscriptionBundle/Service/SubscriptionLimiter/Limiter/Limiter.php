<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\LimiterData;

class Limiter
{
    /**
     * @var LimiterPerformer
     */
    private $limiterPerformer;

    public function __construct(LimiterPerformer $limiterPerformer)
    {
        $this->limiterPerformer = $limiterPerformer;
    }

    /**
     * @param LimiterData $limiterData
     */
    public function decrProcessingSlots(LimiterData $limiterData): void
    {
        if($limiterData->getSubscriptionConstraint()) {
            $this->limiterPerformer->decrAffiliateProcessingSlotsWithLock($limiterData);
        }
        if($limiterData->getCarrier()) {
            $this->limiterPerformer->decrCarrierProcessingSlotsWithLock($limiterData);
        }
    }

    /**
     * @param LimiterData $limiterData
     */
    public function incrProcessingSlots(LimiterData $limiterData)
    {
        if($limiterData->getSubscriptionConstraint()) {
            $this->limiterPerformer->incrAffiliateProcessingSlotsWithLock($limiterData);
        }
        if($limiterData->getCarrier()) {
            $this->limiterPerformer->incrCarrierProcessingSlotsWithLock($limiterData);
        }
    }

    /**
     * @param LimiterData $limiterData
     */
    public function decrSubscriptionSlots(LimiterData $limiterData)
    {
        if($limiterData->getSubscriptionConstraint()) {
            $this->limiterPerformer->decrAffiliateSubscriptionSlotsWithLock($limiterData);
        }
        if($limiterData->getCarrier()) {
            $this->limiterPerformer->decrCarrierSubscriptionSlotsWithLock($limiterData);
        }
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return mixed
     */
    public function getCarrierProcessingSlots(LimiterData $limiterData): int
    {
        return $this->limiterPerformer->getCarrierSlots($limiterData)[LimiterStructureGear::PROCESSING_SLOTS];
    }

    /**
     * @param LimiterData $limiterData
     *
     * @return int
     */
    public function getAffiliateProcessingSlots(LimiterData $limiterData): int
    {
        return $this->limiterPerformer->getCarrierAffiliateConstraintSlots($limiterData)[LimiterStructureGear::PROCESSING_SLOTS];
    }
}