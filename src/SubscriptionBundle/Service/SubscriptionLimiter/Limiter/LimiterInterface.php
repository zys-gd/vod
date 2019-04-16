<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\LimiterData;

interface LimiterInterface
{
    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function setCarrierConstraint(LimiterData $limiterData): array;

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function setCarrierAffiliateConstraint(LimiterData $limiterData): array;

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function updateCarrierSlots(LimiterData $limiterData): array;

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function updateCarrierAffiliateConstraintSlots(LimiterData $limiterData): array;

    /**
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function getCarrierSlots(array $limiter, LimiterData $limiterData): array;

    /**
     * @param array       $limiter
     * @param LimiterData $limiterData
     *
     * @return array
     */
    public function getCarrierAffiliateConstraintSlots(array $limiter, LimiterData $limiterData): array;
}