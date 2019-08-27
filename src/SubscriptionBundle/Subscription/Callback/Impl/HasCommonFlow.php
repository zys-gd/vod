<?php

namespace SubscriptionBundle\Subscription\Callback\Impl;

use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.10.18
 * Time: 9:45
 */
interface HasCommonFlow
{
    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse);

    public function getUser(string $msisdn): ?User;
}