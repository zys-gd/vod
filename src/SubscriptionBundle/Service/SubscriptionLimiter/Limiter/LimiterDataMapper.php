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
    public function saveCarrierConstraint(CarrierLimiterData $carrierLimiterData): array
    {
        $slots = $this->filterFromNull([
            self::SLOTS                   => $carrierLimiterData->getCarrier()->getNumberOfAllowedSubscriptionsByConstraint(),
            self::PROCESSING_SLOTS        => $carrierLimiterData->getProcessingSlots() ?? $carrierLimiterData->getCarrier()->getNumberOfAllowedSubscriptionsByConstraint(),
            self::OPEN_SUBSCRIPTION_SLOTS => $carrierLimiterData->getOpenSubscriptionSlots() ?? $carrierLimiterData->getCarrier()->getNumberOfAllowedSubscriptionsByConstraint()
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
    public function saveCarrierAffiliateConstraint(AffiliateLimiterData $affiliateLimiterData): array
    {
        $slots = $this->filterFromNull([
            self::SLOTS                   => $affiliateLimiterData->getConstraintByAffiliate()->getNumberOfActions(),
            self::PROCESSING_SLOTS        => $affiliateLimiterData->getProcessingSlots() ?? $affiliateLimiterData->getConstraintByAffiliate()->getNumberOfActions(),
            self::OPEN_SUBSCRIPTION_SLOTS => $affiliateLimiterData->getOpenSubscriptionSlots() ?? $affiliateLimiterData->getConstraintByAffiliate()->getNumberOfActions()
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
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function updateCarrierSlots(CarrierLimiterData $carrierLimiterData): array
    {
        $slots = $this->filterFromNull([
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
     * @param array              $limiter
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function getCarrierSlots(array $limiter, CarrierLimiterData $carrierLimiterData): array
    {
        try {
            return [
                self::PROCESSING_SLOTS        => $limiter[self::KEY][$carrierLimiterData->getCarrier()->getBillingCarrierId()][self::PROCESSING_SLOTS] ?? null,
                self::OPEN_SUBSCRIPTION_SLOTS => $limiter[self::KEY][$carrierLimiterData->getCarrier()->getBillingCarrierId()][self::OPEN_SUBSCRIPTION_SLOTS] ?? null
            ];
        } catch (\ErrorException $e) {
            // smth throw
        }
    }

    /**
     * @param array                $limiter
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function getCarrierAffiliateConstraintSlots(array $limiter,
        AffiliateLimiterData $affiliateLimiterData): array
    {
        try {
            return [
                self::PROCESSING_SLOTS        => $limiter[self::KEY][$affiliateLimiterData->getBillingCarrierId()][$affiliateLimiterData->getAffiliate()->getUuid()][$affiliateLimiterData->getConstraintByAffiliate()->getUuid()][self::PROCESSING_SLOTS] ?? null,
                self::OPEN_SUBSCRIPTION_SLOTS => $limiter[self::KEY][$affiliateLimiterData->getBillingCarrierId()][$affiliateLimiterData->getAffiliate()->getUuid()][$affiliateLimiterData->getConstraintByAffiliate()->getUuid()][self::OPEN_SUBSCRIPTION_SLOTS] ?? null
            ];
        } catch (\ErrorException $e) {
            // smth throw
        }
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
}