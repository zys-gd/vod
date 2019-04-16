<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;

use SubscriptionBundle\Service\SubscriptionLimiter\DTO\LimiterData;

class LimiterStructureGear/* implements LimiterInterface*/
{
    const KEY                     = 'subscription_limiter';
    const SLOTS                   = 'slots';
    const PROCESSING_SLOTS        = 'processing_slots';
    const OPEN_SUBSCRIPTION_SLOTS = 'open_subscription_slots';

    /**
     * LimiterStructureGear constructor.
     */
    /*public function __construct(array $limiter,
        int $limiterData->billingCarrierId,
        string $limiterData->getAffiliate()->getUuid(),
        string $limiterData->getSubscriptionConstraint()->getUuid())
    {
        return $this;
    }*/

    /**
     * @param LimiterData $limiterData ->billingCarrierId
     *
     * @return array
     */
    public function setCarrierConstraint(LimiterData $limiterData): array
    {
        $slots = $this->filterFromNull([
            self::SLOTS                   => $limiterData->getCarrier()->getNumberOfAllowedSubscriptionsByConstraint(),
            self::PROCESSING_SLOTS        => $limiterData->getCarrierProcessingSlots() ?? $limiterData->getCarrier()->getNumberOfAllowedSubscriptionsByConstraint(),
            self::OPEN_SUBSCRIPTION_SLOTS => $limiterData->getCarrierOpenSubscriptionSlots() ?? $limiterData->getCarrier()->getNumberOfAllowedSubscriptionsByConstraint()
        ]);

        $limiterStructure = [
            self::KEY => [
                $limiterData->getCarrier()->getBillingCarrierId() => $slots
            ]
        ];
        return $limiterStructure;
    }

    /**
     * @param LimiterData $limiterData ->constraintUuid
     *
     * @return array
     */
    public function setCarrierAffiliateConstraint(LimiterData $limiterData): array
    {
        $slots = $this->filterFromNull([
            self::SLOTS                   => $limiterData->getSubscriptionConstraint()->getNumberOfActions(),
            self::PROCESSING_SLOTS        => $limiterData->getAffiliateProcessingSlots() ?? $limiterData->getSubscriptionConstraint()->getNumberOfActions(),
            self::OPEN_SUBSCRIPTION_SLOTS => $limiterData->getAffiliateOpenSubscriptionSlots() ?? $limiterData->getSubscriptionConstraint()->getNumberOfActions()
        ]);

        $limiterStructure = [
            self::KEY => [
                $limiterData->getCarrier()->getBillingCarrierId() => [
                    $limiterData->getAffiliate()->getUuid() => [
                        $limiterData->getSubscriptionConstraint()->getUuid() => $slots
                    ]
                ]
            ]
        ];
        return $limiterStructure;
    }

    /**
     * @param LimiterData $limiterData ->billingCarrierId
     *
     * @return array
     */
    public function updateCarrierSlots(LimiterData $limiterData): array
    {
        $slots = $this->filterFromNull([
            self::PROCESSING_SLOTS        => $limiterData->getCarrierProcessingSlots(),
            self::OPEN_SUBSCRIPTION_SLOTS => $limiterData->getCarrierOpenSubscriptionSlots()
        ]);

        $limiterStructure = [
            self::KEY => [
                $limiterData->getCarrier()->getBillingCarrierId() => $slots
            ]
        ];
        return $limiterStructure;
    }

    /**
     * @param LimiterData $limiterData ->constraintUuid
     *
     * @return array
     */
    public function updateCarrierAffiliateConstraintSlots(LimiterData $limiterData): array
    {
        $slots = $this->filterFromNull([
            self::PROCESSING_SLOTS        => $limiterData->getAffiliateProcessingSlots(),
            self::OPEN_SUBSCRIPTION_SLOTS => $limiterData->getAffiliateOpenSubscriptionSlots()
        ]);

        $limiterStructure = [
            self::KEY => [
                $limiterData->getCarrier()->getBillingCarrierId() => [
                    $limiterData->getAffiliate()->getUuid() => [
                        $limiterData->getSubscriptionConstraint()->getUuid() => $slots
                    ]
                ]
            ]
        ];
        return $limiterStructure;
    }

    /**
     * @param array       $limiter
     * @param LimiterData $limiterData ->billingCarrierId
     *
     * @return array
     */
    public function getCarrierSlots(array $limiter, LimiterData $limiterData): array
    {
        try {
            return [
                self::PROCESSING_SLOTS        => $limiter[self::KEY][$limiterData->getCarrier()->getBillingCarrierId()][self::PROCESSING_SLOTS],
                self::OPEN_SUBSCRIPTION_SLOTS => $limiter[self::KEY][$limiterData->getCarrier()->getBillingCarrierId()][self::OPEN_SUBSCRIPTION_SLOTS]
            ];
        } catch (\ErrorException $e) {
            // smth throw
        }
    }

    /**
     * @param array       $limiter
     * @param LimiterData $limiterData ->constraintUuid
     *
     * @return array
     */
    public function getCarrierAffiliateConstraintSlots(array $limiter, LimiterData $limiterData): array
    {
        try {
            return [
                self::PROCESSING_SLOTS        => $limiter[self::KEY][$limiterData->getCarrier()->getBillingCarrierId()][$limiterData->getAffiliate()->getUuid()][$limiterData->getSubscriptionConstraint()->getUuid()][self::PROCESSING_SLOTS],
                self::OPEN_SUBSCRIPTION_SLOTS => $limiter[self::KEY][$limiterData->getCarrier()->getBillingCarrierId()][$limiterData->getAffiliate()->getUuid()][$limiterData->getSubscriptionConstraint()->getUuid()][self::OPEN_SUBSCRIPTION_SLOTS]
            ];
        } catch (\ErrorException $e) {
            // smth throw
        }
    }

    private function filterFromNull(array $array): array
    {
        return array_filter($array, function ($value){
            return !is_null($value);
        });
    }
}