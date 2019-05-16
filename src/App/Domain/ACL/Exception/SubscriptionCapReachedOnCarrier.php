<?php

namespace App\Domain\ACL\Exception;

use App\Domain\Entity\Carrier;

/**
 * Class SubscriptionCapReachedOnCarrier
 */
class SubscriptionCapReachedOnCarrier extends AccessException
{
    /**
     * @var Carrier
     */
    private $carrier;

    /**
     * SubscriptionCapReachedOnCarrier constructor
     *
     * @param Carrier $carrier
     */
    public function __construct(Carrier $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @return Carrier
     */
    public function getCarrier(): Carrier
    {
        return $this->carrier;
    }
}