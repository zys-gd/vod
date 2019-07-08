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
     * @param User $user
     *
     * @return bool
     */
    public function isAffiliateTrackedForSub(ProcessResult $result, User $user): bool;

    /**
     * @param ProcessResult $result
     * @param User $user
     *
     * @return bool
     */
    public function isAffiliateTrackedForResub(ProcessResult $result, User $user): bool;
}