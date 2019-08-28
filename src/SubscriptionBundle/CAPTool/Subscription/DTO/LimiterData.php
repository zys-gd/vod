<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.05.19
 * Time: 13:18
 */

namespace SubscriptionBundle\CAPTool\Subscription\DTO;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class LimiterData
{
    /**
     * @var CarrierInterface
     */
    private $carrier;
    /**
     * @var ConstraintByAffiliate
     */
    private $constraintByAffiliate;

    /**
     * LimiterData constructor.
     * @param CarrierInterface           $carrier
     * @param ConstraintByAffiliate|null $constraintByAffiliate
     */
    public function __construct(CarrierInterface $carrier, ConstraintByAffiliate $constraintByAffiliate = null)
    {
        $this->carrier               = $carrier;
        $this->constraintByAffiliate = $constraintByAffiliate;
    }

    /**
     * @return CarrierInterface
     */
    public function getCarrier(): CarrierInterface
    {
        return $this->carrier;
    }

    /**
     * @return ConstraintByAffiliate
     */
    public function getConstraintByAffiliate(): ?ConstraintByAffiliate
    {
        return $this->constraintByAffiliate;
    }


}