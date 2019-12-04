<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.08.19
 * Time: 14:33
 */

namespace SubscriptionBundle\Subscription\Common;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class ProcessResultSuccessChecker
{
    public function isSuccessful(ProcessResult $processResult): bool
    {
        return !$processResult->isFailed() && !$processResult->getError();
    }

    /**
     * @param ProcessResult $processResult
     *
     * @return bool
     */
    public function isSuccessfulForCapTrack(ProcessResult $processResult): bool
    {
        return $processResult->isSuccessful() && !$processResult->getError();
    }
}