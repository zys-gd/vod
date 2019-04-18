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
            $this->limiterPerformer->decrCarrierProcessingSlotsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId());
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->decrAffiliateProcessingSlotsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid());
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
            $this->limiterPerformer->incrCarrierProcessingSlotsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId());
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->incrAffiliateProcessingSlotsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid());
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
            $this->limiterPerformer->decrCarrierSubscriptionSlotsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId());
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->decrAffiliateSubscriptionSlotsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid());
        }
    }
}