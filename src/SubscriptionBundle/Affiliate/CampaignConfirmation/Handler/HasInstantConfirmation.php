<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 14:56
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Handler;


use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\AbstractResult;
use SubscriptionBundle\Affiliate\DTO\UserInfo;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;

interface HasInstantConfirmation
{
    public function doConfirm(
        AffiliateInterface $affiliate,
        CampaignInterface $campaign,
        array $rawCampaignData,
        Subscription $subscription,
        UserInfo $userInfo
    ): AbstractResult;
}