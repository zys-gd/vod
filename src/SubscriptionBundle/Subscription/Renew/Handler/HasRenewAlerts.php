<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.03.19
 * Time: 12:48
 */

namespace SubscriptionBundle\Subscription\Renew\Handler;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\Entity\Subscription;

interface HasRenewAlerts
{
    public function onRenewAlert(Subscription $subscription, CarrierInterface $carrier): void;
}