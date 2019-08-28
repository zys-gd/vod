<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.10.18
 * Time: 13:47
 */

namespace SubscriptionBundle\Subscription\Callback\Impl;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

interface HasCustomTrackingRules
{
    public function isNeedToBeTracked(ProcessResult $result): bool;
}