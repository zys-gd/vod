<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;

use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface HasCustomAffiliateTrackingRules
 */
interface HasCustomAffiliateTrackingRules
{
    /**
     * @param ProcessResult     $result
     * @param CampaignInterface $campaign
     *
     * @return bool
     */
    public function isAffiliateTrackedForSub(ProcessResult $result, CampaignInterface $campaign): bool;

    /**
     * @param ProcessResult $result
     *
     * @return bool
     */
    public function isAffiliateTrackedForResub(ProcessResult $result): bool;
}