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
     * @param int $event
     * @param bool $isSuccess
     * @param string $fullUrl
     * @param UserInfo $info
     * @param CampaignInterface $campaign
     * @param Subscription $subscription
     * @param array $campaignParams
     * @param string|null $message
     *
     * @return AffiliateLog
     *
     * @throws \Exception
     */
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
        $affiliateLog = new AffiliateLog(UuidGenerator::generate());

        $affiliateLog->setEvent($event);
        $affiliateLog->setStatus($isSuccess ? AffiliateLog::STATUS_SUCCESS : AffiliateLog::STATUS_FAILURE);
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