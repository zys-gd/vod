<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.19
 * Time: 15:27
 */

namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use IdentificationBundle\Entity\CarrierInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CarrierCapChecker constructor.
     * @param LimiterStorage      $limiterDataStorage
     * @param StorageKeyGenerator $storageKeyGenerator
     * @param LoggerInterface     $logger
     */
    public function __construct(LimiterStorage $limiterDataStorage, StorageKeyGenerator $storageKeyGenerator, LoggerInterface $logger)
    {
        $this->limiterDataStorage  = $limiterDataStorage;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->logger              = $logger;
    }

    public function isCapReachedForCarrier(CarrierInterface $carrier): bool
    {
        $key = $this->storageKeyGenerator->generateKey($carrier);

        $pending      = $this->limiterDataStorage->getPendingSubscriptionAmount($key);
        $finished     = $this->limiterDataStorage->getFinishedSubscriptionAmount($key);
        $totalCount   = $pending + $finished;
        $carrierLimit = $carrier->getNumberOfAllowedSubscriptionsByConstraint();

        $this->logger->debug('Carrier cap check', [
            'pending'      => $pending,
            'finished'     => $finished,
            'carrierLimit' => $carrierLimit
        ]);

        return $totalCount > $carrierLimit;

    }

    public function isCapReachedForAffiliate(\SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate $constraintByAffiliate): bool
    {
        $key = $this->storageKeyGenerator->generateAffiliateConstraintKey($constraintByAffiliate);

        $pending        = $this->limiterDataStorage->getPendingSubscriptionAmount($key);
        $finished       = $this->limiterDataStorage->getFinishedSubscriptionAmount($key);
        $totalCount     = $pending + $finished;
        $affiliateLimit = $constraintByAffiliate->getNumberOfActions();


        $this->logger->debug('Affiliate cap check', [
            'pending'        => $pending,
            'finished'       => $finished,
            'affiliateLimit' => $affiliateLimit
        ]);


        return $totalCount > $affiliateLimit;
    }
}