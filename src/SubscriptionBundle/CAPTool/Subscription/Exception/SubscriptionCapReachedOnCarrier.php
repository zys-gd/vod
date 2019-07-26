<?php

namespace SubscriptionBundle\CAPTool\Subscription\Exception;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;

/**
 * Class SubscriptionCapReachedOnCarrier
 */
class SubscriptionCapReachedOnCarrier extends CapToolAccessException
{
    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * SubscriptionCapReachedOnCarrier constructor
     *
     * @param CarrierInterface $carrier
     */
    public function __construct(CarrierInterface $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @return CarrierInterface
     */
    public function getCarrier(): CarrierInterface
    {
        return $this->carrier;
    }
}