<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.05.19
 * Time: 13:34
 */

namespace SubscriptionBundle\Service\VisitCAPTool;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class VisitChecker
{
    /**
     * @var VisitStorage
     */
    private $visitStorage;
    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    /**
     * VisitChecker constructor.
     * @param VisitStorage $visitStorage
     * @param KeyGenerator $keyGenerator
     */
    public function __construct(VisitStorage $visitStorage, KeyGenerator $keyGenerator)
    {
        $this->visitStorage = $visitStorage;
        $this->keyGenerator = $keyGenerator;
    }

    public function isCapReached(CarrierInterface $carrier, ConstraintByAffiliate $constraintByAffiliate): bool
    {
        $key = $this->keyGenerator->generateVisitKey($carrier, $constraintByAffiliate->getAffiliate());

        $count = $this->visitStorage->getVisitCount($key);

        return $constraintByAffiliate->getNumberOfActions() <= $count;

    }
}