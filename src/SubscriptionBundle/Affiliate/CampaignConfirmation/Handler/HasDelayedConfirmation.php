<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 15:42
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Handler;


use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\AbstractResult;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;

interface HasDelayedConfirmation
{

    public function doConfirm(AffiliateLog $affiliateLog): AbstractResult;

    /**
     * @return AffiliateLog[]
     */
    public function getBatchOfLogs(): array;
}