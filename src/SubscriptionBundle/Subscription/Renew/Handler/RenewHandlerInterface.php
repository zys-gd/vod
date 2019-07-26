<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Subscription\Renew\Handler;


interface RenewHandlerInterface
{
    public function canHandle(\CommonDataBundle\Entity\Interfaces\CarrierInterface $carrier): bool;
}