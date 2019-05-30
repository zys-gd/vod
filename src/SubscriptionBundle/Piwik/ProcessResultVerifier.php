<?php


namespace SubscriptionBundle\Piwik;

use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class ProcessResultVerifier
{
    /**
     * @param ProcessResult $bfResponse
     *
     * @return bool
     */
    public function cantTrackSubscription(ProcessResult $bfResponse): bool
    {
        if ($bfResponse->getStatus() === ProcessResult::STATUS_FAILED
            && $bfResponse->getError() === ProcessResult::ERROR_CANCELED
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param ProcessResult $bfResponse
     *
     * @return bool
     */
    public function cantTrackUnsubscription(ProcessResult $bfResponse): bool
    {
        if (
            $bfResponse->getError() != ProcessResult::ERROR_USER_TIMEOUT
            && !($bfResponse->getError() == ProcessResult::ERROR_CANCELED)
            && (
                $bfResponse->getType() !== 'unsubscribe'
                || !in_array($bfResponse->getStatus(), ['successful', 'failed', 'ok'])
            )

        ) {
            return true;
        }

        return false;
    }

    /**
     * @param ProcessResult $bfResponse
     *
     * @return bool
     */
    public function isSuccessSubscribe(ProcessResult $bfResponse): bool
    {
        return $bfResponse->getStatus() === 'successful';
    }

    /**
     * @param ProcessResult $bfResponse
     *
     * @return bool
     */
    public function isSuccessUnsubscribe(ProcessResult $bfResponse): bool
    {
        if($bfResponse->getError() == ProcessResult::ERROR_BATCH_LIMIT_EXCEEDED) {
            return false;
        }

        if($bfResponse->getError() == ProcessResult::ERROR_USER_TIMEOUT) {
            return false;
        }

        if (!in_array($bfResponse->getStatus(),  ['successful', 'ok'])) {
            return false;
        }

        return true;
    }
}