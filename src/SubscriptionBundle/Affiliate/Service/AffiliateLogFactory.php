<?php

namespace SubscriptionBundle\Affiliate\Service;

use ExtrasBundle\Utils\UuidGenerator;
use SubscriptionBundle\Affiliate\DTO\UserInfo;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;

/**
 * Class AffiliateLogFactory
 */
class AffiliateLogFactory
{
    /**
     * @param int               $status
     * @param string            $fullUrl
     * @param UserInfo          $info
     * @param CampaignInterface $campaign
     * @param Subscription      $subscription
     * @param array             $userCampaignParams
     * @param string|null       $message
     *
     * @return AffiliateLog
     *
     * @throws \Exception
     */
    public function create(
        int $status,
        string $fullUrl,
        UserInfo $info,
        CampaignInterface $campaign,
        Subscription $subscription,
        array $userCampaignParams,
        string $message = null
    ): AffiliateLog
    {
        $affiliateLog = new AffiliateLog(UuidGenerator::generate());

        $affiliateLog->setEvent(AffiliateLog::EVENT_SUBSCRIBE);
        $affiliateLog->setStatus($status);
        $affiliateLog->setUrl($fullUrl);
        $affiliateLog->setLog($message);
        $affiliateLog->setUserIp($info->getUserIp());
        $affiliateLog->setUserMsisdn($info->getMsidsn());
        $affiliateLog->setCampaignToken($campaign->getCampaignToken());
        $affiliateLog->setCampaignParams($userCampaignParams);
        $affiliateLog->setSubscriptionId($subscription->getUuid());

        return $affiliateLog;
    }
}