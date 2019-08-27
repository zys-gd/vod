<?php

namespace SubscriptionBundle\Subscription\Subscribe\Handler;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 14:04
 */
interface SubscriptionHandlerInterface
{
    public function canHandle(\CommonDataBundle\Entity\Interfaces\CarrierInterface $carrier): bool;

}