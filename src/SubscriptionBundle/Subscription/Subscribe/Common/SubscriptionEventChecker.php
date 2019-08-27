<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.08.19
 * Time: 12:39
 */

namespace SubscriptionBundle\Subscription\Subscribe\Common;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class SubscriptionEventChecker
{

    public function isSubscribeNeedToBeTracked(ProcessResult $processResult): bool
    {
        if ($processResult->getError() === ProcessResult::ERROR_CANCELED) {
            return false;
        }

        return true;
    }

    public function isResubscribeNeedToBeTracked()
    {

    }
}