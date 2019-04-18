<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;

use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;

class LimiterDataMapper
{
    const KEY                     = 'subscription_limiter';
    const SLOTS                   = 'slots';
    const PROCESSING_SLOTS        = 'processing_slots';
    const OPEN_SUBSCRIPTION_SLOTS = 'open_subscription_slots';

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function convertCarrierLimiterData2Array(CarrierLimiterData $carrierLimiterData): array
    {
        $slots = $this->filterFromNull([
            self::SLOTS                   => $carrierLimiterData->getCarrier()->getNumberOfAllowedSubscriptionsByConstraint(),
            self::PROCESSING_SLOTS        => $carrierLimiterData->getProcessingSlots(),
            self::OPEN_SUBSCRIPTION_SLOTS => $carrierLimiterData->getOpenSubscriptionSlots()
        ]);

        $limiterStructure = [
            self::KEY => [
                $carrierLimiterData->getCarrier()->getBillingCarrierId() => $slots
            ]
        ];
        return $limiterStructure;
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function convertCarrierAffiliateConstraint(AffiliateLimiterData $affiliateLimiterData): array
    {
        $slots = $this->filterFromNull([
            self::SLOTS                   => $affiliateLimiterData->getConstraintByAffiliate()->getNumberOfActions(),
            self::PROCESSING_SLOTS        => $affiliateLimiterData->getProcessingSlots(),
            self::OPEN_SUBSCRIPTION_SLOTS => $affiliateLimiterData->getOpenSubscriptionSlots()
        ]);

        $limiterStructure = [
            self::KEY => [
                $affiliateLimiterData->getBillingCarrierId() => [
                    $affiliateLimiterData->getAffiliate()->getUuid() => [
                        $affiliateLimiterData->getConstraintByAffiliate()->getUuid() => $slots
                    ]
                ]
            ]
        ];
        return $limiterStructure;
    }

    /**
     * @param int   $billingCarrierId
     * @param array $slots
     *
     * @return array
     */
    public function updateCarrierSlots(int $billingCarrierId, array $slots): array
    {
        $slots = $this->filterFromNull([
            self::PROCESSING_SLOTS        => $slots[self::PROCESSING_SLOTS],
            self::OPEN_SUBSCRIPTION_SLOTS => $slots[self::OPEN_SUBSCRIPTION_SLOTS]
        ]);

        $limiterStructure = [
            self::KEY => [
                $billingCarrierId => $slots
            ]
        ];
        return $limiterStructure;
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function updateCarrierAffiliateConstraintSlots(AffiliateLimiterData $affiliateLimiterData): array
    {
        $slots = $this->filterFromNull([
            self::PROCESSING_SLOTS        => $affiliateLimiterData->getProcessingSlots(),
            self::OPEN_SUBSCRIPTION_SLOTS => $affiliateLimiterData->getOpenSubscriptionSlots()
        ]);

        $limiterStructure = [
            self::KEY => [
                $affiliateLimiterData->getBillingCarrierId() => [
                    $affiliateLimiterData->getAffiliate()->getUuid() => [
                        $affiliateLimiterData->getConstraintByAffiliate()->getUuid() => $slots
                    ]
                ]
            ]
        ];
        return $limiterStructure;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    private function filterFromNull(array $array): array
    {
        return array_filter($array, function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @param array $redisData
     * @param int   $billingCarrierId
     *
     * @return array
     */
    public function extractCarrierSlots(array $redisData, int $billingCarrierId): array
    {
        try {
            return [
                self::PROCESSING_SLOTS        => $redisData[self::KEY][$billingCarrierId][self::PROCESSING_SLOTS] ?? null,
                self::OPEN_SUBSCRIPTION_SLOTS => $redisData[self::KEY][$billingCarrierId][self::OPEN_SUBSCRIPTION_SLOTS] ?? null
            ];
        } catch (\ErrorException $e) {
            // smth throw
        }
    }

    /**
     * @param array  $redisData
     * @param int    $billingCarrierId
     * @param string $affiliateUuid
     * @param string $constraintUuid
     *
     * @return array
     */
    public function extractAffiliateSlots(array $redisData,
        int $billingCarrierId,
        string $affiliateUuid,
        string $constraintUuid): array
    {
        try {
            return [
                self::PROCESSING_SLOTS        => $redisData[self::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid][self::PROCESSING_SLOTS] ?? null,
                self::OPEN_SUBSCRIPTION_SLOTS => $redisData[self::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid][self::OPEN_SUBSCRIPTION_SLOTS] ?? null
            ];
        } catch (\ErrorException $e) {
            // smth throw
        }
    }
}