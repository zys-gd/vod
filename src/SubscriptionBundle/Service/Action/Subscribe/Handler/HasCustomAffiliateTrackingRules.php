<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;

use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

/**
 * Interface HasCustomAffiliateTrackingRules
 */
interface HasCustomAffiliateTrackingRules
{
    /**
     * @param ProcessResult $result
     *
     * @return bool
     */
    public function isAffiliateTrackedForSub(ProcessResult $result): bool;

    /**
     * @param ProcessResult $result
     *
     * @return bool
     */
    public function isAffiliateTrackedForResub(ProcessResult $result): bool;
}