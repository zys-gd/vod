<?php

namespace SubscriptionBundle\Service\CAPTool\Exception;

use IdentificationBundle\Entity\CarrierInterface;

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