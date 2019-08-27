<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Subscription\Renew\Handler;


use SubscriptionBundle\Entity\Subscription;

class DefaultHandler implements RenewHandlerInterface, HasCommonFlow
{

    public function canHandle(\CommonDataBundle\Entity\Interfaces\CarrierInterface $carrier): bool
    {
        return true;
    }

    public function onRenewSendSuccess(Subscription $subscription, int $processId): void
    {
        // TODO: Implement onRenewSendSuccess() method.
    }

    public function onRenewSendFailure(Subscription $subscription, string $errorText): void
    {
        // TODO: Implement onRenewSendFailure() method.
    }
}