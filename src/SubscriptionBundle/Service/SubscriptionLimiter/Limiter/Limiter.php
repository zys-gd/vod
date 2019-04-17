<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;

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
     * @param CarrierLimiterData|null   $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     */
    public function decrProcessingSlots(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData): void
    {
        if ($carrierLimiterData) {
            $this->limiterPerformer->decrCarrierProcessingSlotsWithLock($carrierLimiterData);
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->decrAffiliateProcessingSlotsWithLock($affiliateLimiterData);
        }
    }

    /**
     * @param CarrierLimiterData|null   $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     */
    public function incrProcessingSlots(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData)
    {
        if ($carrierLimiterData) {
            $this->limiterPerformer->incrCarrierProcessingSlotsWithLock($carrierLimiterData);
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->incrAffiliateProcessingSlotsWithLock($affiliateLimiterData);
        }
    }

    /**
     * @param CarrierLimiterData|null   $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     */
    public function decrSubscriptionSlots(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData)
    {
        if ($carrierLimiterData) {
            $this->limiterPerformer->decrCarrierSubscriptionSlotsWithLock($carrierLimiterData);
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->decrAffiliateSubscriptionSlotsWithLock($affiliateLimiterData);
        }
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return mixed
     */
    public function getCarrierProcessingSlots(CarrierLimiterData $carrierLimiterData): int
    {
        return $this->limiterPerformer->getCarrierSlots($carrierLimiterData)[LimiterDataMapper::PROCESSING_SLOTS];
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return int
     */
    public function getAffiliateProcessingSlots(AffiliateLimiterData $affiliateLimiterData): int
    {
        return $this->limiterPerformer->getCarrierAffiliateConstraintSlots($affiliateLimiterData)[LimiterDataMapper::PROCESSING_SLOTS];
    }
}