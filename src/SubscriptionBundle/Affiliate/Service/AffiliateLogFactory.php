<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 14:42
 */

namespace SubscriptionBundle\Affiliate\Service;


use SubscriptionBundle\Affiliate\DTO\UserInfo;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;

class AffiliateLogFactory
{
    public function create(
        int $event,
        bool $isSuccess,
        string $fullUrl,
        UserInfo $info,
        CampaignInterface $campaign,
        Subscription $subscription,
        array $campaignParams,
        string $message = null
    ): AffiliateLog
    {

        $affiliateLog = new AffiliateLog();

        $affiliateLog->setEvent($event);
        $affiliateLog->setStatus($isSuccess ?
                                     AffiliateLog::STATUS_SUCCESS :
                                     AffiliateLog::STATUS_FAILURE
        );
        $affiliateLog->setUrl($fullUrl);
        $affiliateLog->setLog($message);
        $affiliateLog->setUserIp($info->getUserIp());
        $affiliateLog->setUserMsisdn($info->getMsidsn());
        $affiliateLog->setCampaignToken($campaign->getCampaignToken());
        $affiliateLog->setCampaignParams($campaignParams);
        $affiliateLog->setSubscriptionId($subscription->getUuid());

        return $affiliateLog;
    }

}