<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.05.19
 * Time: 14:00
 */

namespace App\Domain\ACL\Exception;


use App\Domain\Entity\Carrier;

class SubscriptionCapReachedOnCarrier extends AccessException
{
    /**
     * @var Carrier
     */
    private $carrier;

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