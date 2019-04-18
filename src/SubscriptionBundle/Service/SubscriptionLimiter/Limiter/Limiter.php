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
    /**
     * @var LimiterDataExtractor
     */
    private $limiterDataExtractor;
    /**
     * @var LimiterDataConverter
     */
    private $limiterDataConverter;

    /**
     * Limiter constructor.
     *
     * @param LimiterPerformer     $limiterPerformer
     * @param LimiterDataExtractor $limiterDataExtractor
     * @param LimiterDataConverter $limiterDataConverter
     */
    public function __construct(LimiterPerformer $limiterPerformer,
        LimiterDataExtractor $limiterDataExtractor,
        LimiterDataConverter $limiterDataConverter)
    {
        $this->limiterPerformer     = $limiterPerformer;
        $this->limiterDataExtractor = $limiterDataExtractor;
        $this->limiterDataConverter = $limiterDataConverter;
    }

    /**
     * @param CarrierLimiterData|null   $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     */
    public function decrProcessingSlots(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData): void
    {
        if ($carrierLimiterData) {
            $slots = $this->limiterDataExtractor->getCarrierSlots($carrierLimiterData);
            if ($slots[LimiterDataConverter::PROCESSING_SLOTS]-- >= 0) {
                $this->limiterPerformer->updateCarrierConstraintsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId(), $slots);
            }
        }

        if ($affiliateLimiterData) {
            $slots = $this->limiterDataExtractor->getAffiliateSlots($affiliateLimiterData);
            if ($slots[LimiterDataConverter::PROCESSING_SLOTS]-- >= 0) {
                $this->limiterPerformer->updateAffiliateConstraintsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid(), $slots);
            }
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
            $slots = $this->limiterDataExtractor->getCarrierSlots($carrierLimiterData);
            $slots[LimiterDataConverter::PROCESSING_SLOTS]++;
            $this->limiterPerformer->updateCarrierConstraintsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId(), $slots);
        }

        if ($affiliateLimiterData) {
            $slots = $this->limiterDataExtractor->getAffiliateSlots($affiliateLimiterData);
            $slots[LimiterDataConverter::PROCESSING_SLOTS]++;
            $this->limiterPerformer->updateAffiliateConstraintsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid(), $slots);
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
            $slots = $this->limiterDataExtractor->getCarrierSlots($carrierLimiterData);
            if ($slots[LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS]-- >= 0) {
                $this->limiterPerformer->updateCarrierConstraintsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId(), $slots);
            }
        }

        if ($affiliateLimiterData) {
            $slots = $this->limiterDataExtractor->getAffiliateSlots($affiliateLimiterData);
            if ($slots[LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS]-- >= 0) {
                $this->limiterPerformer->updateAffiliateConstraintsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid(), $slots);
            }
        }
    }
}