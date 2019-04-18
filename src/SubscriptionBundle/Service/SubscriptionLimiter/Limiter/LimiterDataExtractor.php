<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;

class LimiterDataExtractor
{
    /**
     * @var LimiterDataStorage
     */
    private $limiterDataStorage;

    /**
     * LimiterDataExtractor constructor.
     *
     * @param LimiterDataStorage $limiterDataStorage
     */
    public function __construct(LimiterDataStorage $limiterDataStorage)
    {
        $this->limiterDataStorage = $limiterDataStorage;
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function getCarrierSlots(CarrierLimiterData $carrierLimiterData): array
    {
        $redisData = $this->limiterDataStorage->getDataFromRedisAsArray();

        $billingCarrierId = $carrierLimiterData->getCarrier()->getBillingCarrierId();

        return [
            LimiterDataConverter::PROCESSING_SLOTS        => $redisData[LimiterDataConverter::KEY][$billingCarrierId][LimiterDataConverter::PROCESSING_SLOTS] ?? null,
            LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS => $redisData[LimiterDataConverter::KEY][$billingCarrierId][LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS] ?? null
        ];
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function getAffiliateSlots(AffiliateLimiterData $affiliateLimiterData): array
    {
        $redisData = $this->limiterDataStorage->getDataFromRedisAsArray();

        $billingCarrierId = $affiliateLimiterData->getBillingCarrierId();
        $affiliateUuid    = $affiliateLimiterData->getAffiliate()->getUuid();
        $constraintUuid   = $affiliateLimiterData->getConstraintByAffiliate()->getUuid();

        return [
            LimiterDataConverter::PROCESSING_SLOTS        => $redisData[LimiterDataConverter::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid][LimiterDataConverter::PROCESSING_SLOTS] ?? null,
            LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS => $redisData[LimiterDataConverter::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid][LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS] ?? null
        ];
    }

    /**
     * @param array $redisData
     * @param int   $billingCarrierId
     *
     * @return array
     */
    public function extractCarrierSlots(array $redisData, int $billingCarrierId): array
    {
        return [
            LimiterDataConverter::PROCESSING_SLOTS        => $redisData[LimiterDataConverter::KEY][$billingCarrierId][LimiterDataConverter::PROCESSING_SLOTS] ?? null,
            LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS => $redisData[LimiterDataConverter::KEY][$billingCarrierId][LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS] ?? null
        ];
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
        return [
            LimiterDataConverter::PROCESSING_SLOTS        => $redisData[LimiterDataConverter::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid][LimiterDataConverter::PROCESSING_SLOTS] ?? null,
            LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS => $redisData[LimiterDataConverter::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid][LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS] ?? null
        ];
    }
}