<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.11.18
 * Time: 10:54
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

interface HasCustomTrackingRules
{

    public function isNeedToBeTrackedForSubscribe(ProcessResult $result): bool;

    public function isNeedToBeTrackedForResubscribe(ProcessResult $result): bool;


}