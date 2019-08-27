<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.08.19
 * Time: 16:42
 */

namespace SubscriptionBundle\Subscription\Unsubscribe;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class UnsubscribeEventChecker
{

    public function isNeedToBeTracked(ProcessResult $processResult): bool
    {
        if ($processResult->getError() == ProcessResult::ERROR_USER_TIMEOUT) {
            return false;
        }

        if ($processResult->getError() == ProcessResult::ERROR_CANCELED) {
            return false;
        }

        return true;
    }
}