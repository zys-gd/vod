<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 12:43
 */

namespace SubscriptionBundle\Blacklist;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Utils\UuidGenerator;
use SubscriptionBundle\Entity\BlackList;

class BlacklistFactory
{

    public function create(CarrierInterface $carrier, string $identifier): BlackList
    {

        $blackList = new BlackList(UuidGenerator::generate());
        $blackList
            ->setBillingCarrierId($carrier->getBillingCarrierId())
            ->setAlias($identifier);

        return $blackList;
    }
}