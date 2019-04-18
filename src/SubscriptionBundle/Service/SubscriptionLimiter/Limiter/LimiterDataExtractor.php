<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;

class LimiterDataExtractor
{
    /**
     * @var LimiterPerformer
     */
    private $limiterPerformer;

    /**
     * LimiterDataExtractor constructor.
     *
     * @param LimiterPerformer $limiterPerformer
     */
    public function __construct(LimiterPerformer $limiterPerformer)
    {
        $this->limiterPerformer = $limiterPerformer;
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function getCarrierSlots(CarrierLimiterData $carrierLimiterData): array
    {
        return $this->limiterPerformer->getCarrierSlots($carrierLimiterData->getCarrier()->getBillingCarrierId());
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function getAffiliateSlots(AffiliateLimiterData $affiliateLimiterData): array
    {
        return $this->limiterPerformer->getCarrierAffiliateConstraintSlots($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid());
    }
}