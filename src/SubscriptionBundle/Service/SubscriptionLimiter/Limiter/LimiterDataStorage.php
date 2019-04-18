<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Locker\LockerFactory;

class LimiterDataStorage
{
    /**
     * @var \Predis\Client|\Redis|\RedisCluster
     */
    private $redis;
    /**
     * @var LimiterDataConverter
     */
    private $limiterDataConverter;
    /**
     * @var LockerFactory
     */
    private $lockerFactory;

    /**
     * LimiterDataStorage constructor.
     *
     * @param                      $redis
     * @param LimiterDataConverter $limiterDataConverter
     * @param LockerFactory        $lockerFactory
     */
    public function __construct($redis,
        LimiterDataConverter $limiterDataConverter,
        LockerFactory $lockerFactory)
    {
        $this->redis                = $redis;
        $this->limiterDataConverter = $limiterDataConverter;
        $this->lockerFactory        = $lockerFactory;
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return array
     */
    public function saveCarrierConstraint(CarrierLimiterData $carrierLimiterData): array
    {
        $data = $this->limiterDataConverter->convertCarrierLimiterData2Array($carrierLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return array
     */
    public function saveCarrierAffiliateConstraint(AffiliateLimiterData $affiliateLimiterData): array
    {
        $data = $this->limiterDataConverter->convertCarrierAffiliateConstraint($affiliateLimiterData);

        return $this->set2Storage($data);
    }

    /**
     * @param int    $billingCarrierId
     * @param string $affiliateUuid
     * @param string $constraintUuid
     */
    public function removeAffiliateConstraint(int $billingCarrierId, string $affiliateUuid, string $constraintUuid)
    {
        try {
            $data = $this->getDataFromRedisAsArray();
            unset($data[LimiterDataConverter::KEY][$billingCarrierId][$affiliateUuid][$constraintUuid]);
            $this->redis->set(LimiterDataConverter::KEY, json_encode($data));
        } catch (\Throwable $e) {
            echo "Cant remove from redis, reason: " . $e->getMessage();
        }
    }

    /**
     * @param int $billingCarrierId
     */
    public function removeCarrierConstraint(int $billingCarrierId)
    {
        try {
            $data = $this->getDataFromRedisAsArray();
            unset($data[LimiterDataConverter::KEY][$billingCarrierId][LimiterDataConverter::PROCESSING_SLOTS]);
            unset($data[LimiterDataConverter::KEY][$billingCarrierId][LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS]);
            $this->redis->set(LimiterDataConverter::KEY, json_encode($data));
        } catch (\Throwable $e) {
            echo "Cant remove from redis, reason: " . $e->getMessage();
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function set2Storage(array $data): array
    {
        if ($this->redis->exists(LimiterDataConverter::KEY)) {
            $data = array_replace_recursive(
                $this->getDataFromRedisAsArray(),
                $data
            );
        }
        $this->redis->set(LimiterDataConverter::KEY, json_encode($data));
        return $data;
    }

    /**
     * @return array
     */
    public function getDataFromRedisAsArray(): array
    {
        return json_decode($this->redis->get(LimiterDataConverter::KEY), true) ?? [];
    }

    /**
     * @param int   $billingCarrierId
     * @param array $slots
     */
    public function updateCarrierConstraints(int $billingCarrierId, array $slots): void
    {
        $data = $this->limiterDataConverter->convertCarrierSlots2Array($billingCarrierId, $slots);
        $this->set2Storage($data);
    }

    /**
     * @param int    $billingCarrierId
     * @param string $affiliateUuid
     * @param string $constraintUuid
     * @param array  $slots
     */
    public function updateAffiliateConstraints(int $billingCarrierId,
        string $affiliateUuid,
        string $constraintUuid,
        array $slots): void
    {
        $data = $this->limiterDataConverter->convertCarrierAffiliateConstraintSlots2Array($billingCarrierId, $affiliateUuid, $constraintUuid, $slots);
        $this->set2Storage($data);
    }
}