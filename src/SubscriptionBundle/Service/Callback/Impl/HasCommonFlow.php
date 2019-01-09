<?php

namespace SubscriptionBundle\Service\Callback\Impl;

use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use UserBundle\Entity\BillableUser;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.10.18
 * Time: 9:45
 */
interface HasCommonFlow
{
    public function afterProcess(Subscription $subscription, BillableUser $billableUser, ProcessResult $processResponse);
}