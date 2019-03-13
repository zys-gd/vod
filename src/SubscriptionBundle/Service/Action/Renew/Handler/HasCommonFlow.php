<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:22
 */

namespace SubscriptionBundle\Service\Action\Renew\Handler;


use SubscriptionBundle\Entity\Subscription;

interface HasCommonFlow
{
    public function onRenewSendSuccess(Subscription $subscription, int $processId): void;

    public function onRenewSendFailure(Subscription $subscription, string $errorText): void;
}