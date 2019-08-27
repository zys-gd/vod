<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.03.19
 * Time: 14:31
 */

namespace SubscriptionBundle\Subscription\Subscribe\Handler;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

interface HasCustomPiwikTrackingRules
{
    public function isPiwikTrackedForSub(ProcessResult $result): bool;

    public function isPiwikTrackedForResub(ProcessResult $result): bool;
}