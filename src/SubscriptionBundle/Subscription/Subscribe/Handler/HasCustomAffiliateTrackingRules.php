<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.11.18
 * Time: 10:54
 */

namespace SubscriptionBundle\Subscription\Subscribe\Handler;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

interface HasCustomAffiliateTrackingRules
{

    public function isAffiliateTrackedForSub(ProcessResult $result): bool;

    public function isAffiliateTrackedForResub(ProcessResult $result): bool;

}