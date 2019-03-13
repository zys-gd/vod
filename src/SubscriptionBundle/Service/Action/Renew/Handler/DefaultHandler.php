<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Service\Action\Renew\Handler;


use SubscriptionBundle\Entity\Subscription;

class DefaultHandler implements RenewHandlerInterface, HasCommonFlow
{

    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
    {
        return true;
    }

    public function onRenewSend(Subscription $subscription, int $processId): void
    {
        // TODO: Implement onRenewSend() method.
    }

    public function onFailure(Subscription $subscription, string $errorText): void
    {
        // TODO: Implement onFailure() method.
    }
}