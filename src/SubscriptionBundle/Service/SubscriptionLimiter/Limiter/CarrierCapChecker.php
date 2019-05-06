<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.19
 * Time: 15:27
 */

namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use IdentificationBundle\Entity\CarrierInterface;

class CarrierCapChecker
{
    /**
     * @var LimiterStorage
     */
    private $limiterDataStorage;
    /**
     * @var StorageKeyGenerator
     */
    private $storageKeyGenerator;

    /**
     * CarrierCapChecker constructor.
     * @param LimiterStorage      $limiterDataStorage
     * @param StorageKeyGenerator $storageKeyGenerator
     */
    public function __construct(LimiterStorage $limiterDataStorage, StorageKeyGenerator $storageKeyGenerator)
    {
        $this->limiterDataStorage  = $limiterDataStorage;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    public function isCapReachedForCarrier(CarrierInterface $carrier): bool
    {
        $key = $this->storageKeyGenerator->generateKey($carrier);

        $pending    = $this->limiterDataStorage->getPendingSubscriptionAmount($key);
        $finished   = $this->limiterDataStorage->getFinishedSubscriptionAmount($key);
        $totalCount = $pending + $finished;

        return $totalCount > $carrier->getNumberOfAllowedSubscriptionsByConstraint();

    }

    public function isCapReachedForAffiliate(\SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate $constraintByAffiliate): bool
    {
        $key = $this->storageKeyGenerator->generateAffiliateConstraintKey($constraintByAffiliate);

        $pending    = $this->limiterDataStorage->getPendingSubscriptionAmount($key);
        $finished   = $this->limiterDataStorage->getFinishedSubscriptionAmount($key);
        $totalCount = $pending + $finished;

        return $totalCount > $constraintByAffiliate->getNumberOfActions();
    }
}