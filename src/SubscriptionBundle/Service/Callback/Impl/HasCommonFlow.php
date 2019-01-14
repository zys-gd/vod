<?php

namespace SubscriptionBundle\Service\Callback\Impl;

use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use IdentificationBundle\Entity\User;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.10.18
 * Time: 9:45
 */
interface HasCommonFlow
{
    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse);
}