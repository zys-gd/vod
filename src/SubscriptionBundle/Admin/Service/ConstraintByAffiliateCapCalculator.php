<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.07.19
 * Time: 12:26
 */

namespace SubscriptionBundle\Admin\Service;


use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CAPTool\Limiter\LimiterStorage;
use SubscriptionBundle\Service\CAPTool\Limiter\StorageKeyGenerator;
use SubscriptionBundle\Service\VisitCAPTool\KeyGenerator;
use SubscriptionBundle\Service\VisitCAPTool\VisitStorage;

class ConstraintByAffiliateCapCalculator
{
    /**
     * @var StorageKeyGenerator
     */
    private $storageKeyGenerator;
    /**
     * @var LimiterStorage
     */
    private $limiterStorage;
    /**
     * @var KeyGenerator
     */
    private $keyGenerator;
    /**
     * @var VisitStorage
     */
    private $visitStorage;


    /**
     * ConstraintByAffiliateCapCalculator constructor.
     * @param StorageKeyGenerator $storageKeyGenerator
     * @param LimiterStorage      $limiterStorage
     * @param KeyGenerator        $keyGenerator
     * @param VisitStorage        $visitStorage
     */
    public function __construct(
        StorageKeyGenerator $storageKeyGenerator,
        LimiterStorage $limiterStorage,
        KeyGenerator $keyGenerator,
        VisitStorage $visitStorage
    )
    {
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->limiterStorage      = $limiterStorage;
        $this->keyGenerator        = $keyGenerator;
        $this->visitStorage        = $visitStorage;
    }

    public function calculateCounter(ConstraintByAffiliate $subject): int
    {

        if ($subject->getCapType() === ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE) {
            $key       = $this->storageKeyGenerator->generateAffiliateConstraintKey($subject);
            $pending   = $this->limiterStorage->getPendingSubscriptionAmount($key);
            $finished  = $this->limiterStorage->getFinishedSubscriptionAmount($key);
            $available = $pending + $finished;
        } else {
            $key       = $this->keyGenerator->generateVisitKey(
                $subject->getCarrier(),
                $subject->getAffiliate()
            );
            $available = $this->visitStorage->getVisitCount($key, new \DateTimeImmutable());
        }

        return (int)$available;

    }
}