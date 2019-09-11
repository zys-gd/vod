<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.09.19
 * Time: 13:58
 */

namespace SubscriptionBundle\Subscription\Subscribe\ProcessStarter;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;

interface SubscribeProcessStarterInterface
{
    public function isSupports(CarrierInterface $carrier): bool;

    public function start(Subscription $subscription, SubscriptionPack $pack, array $additionalData = []): ProcessResult;

    public function startResubscribe(Subscription $subscription, SubscriptionPack $plan, array $additionalData): ProcessResult;

}