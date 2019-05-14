<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.05.19
 * Time: 16:10
 */

namespace SubscriptionBundle\Affiliate\CapConstraint;


use App\Domain\Entity\Campaign;
use IdentificationBundle\Entity\CarrierInterface;

class VisitTracker
{
    /**
     * @var ConstraintValidityChecker
     */
    private $constraintValidityChecker;
    /**
     * @var VisitStorage
     */
    private $visitStorage;
    /**
     * @var KeyGenerator
     */
    private $keyGenerator;


    /**
     * VisitTracker constructor.
     * @param ConstraintValidityChecker $constraintValidityChecker
     * @param VisitStorage              $visitStorage
     * @param KeyGenerator              $keyGenerator
     */
    public function __construct(ConstraintValidityChecker $constraintValidityChecker, VisitStorage $visitStorage, KeyGenerator $keyGenerator)
    {
        $this->constraintValidityChecker = $constraintValidityChecker;
        $this->visitStorage              = $visitStorage;
        $this->keyGenerator              = $keyGenerator;
    }

    public function trackVisit(CarrierInterface $carrier, Campaign $campaign, string $visitInfo)
    {
        $affiliate             = $campaign->getAffiliate();
        $constraintByAffiliate = null;

        $key = $this->keyGenerator->generateVisitKey($carrier, $affiliate);
        $this->visitStorage->storeVisit($key, $visitInfo);
    }

}