<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.08.19
 * Time: 11:46
 */

namespace SubscriptionBundle\Piwik\Service;


use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\Entity\Subscription;

class AffiliateStringProvider
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    /**
     * AffiliateStringProvider constructor.
     * @param CampaignExtractor $campaignExtractor
     */
    public function __construct(CampaignExtractor $campaignExtractor)
    {
        $this->campaignExtractor = $campaignExtractor;
    }

    /**
     * @param Subscription $subscription
     *
     * @return string|null
     */
    public function getAffiliateString(Subscription $subscription): ?string
    {
        try {
            $campaign  = $this->campaignExtractor->getCampaignForSubscription($subscription);
            $affiliate = $campaign->getAffiliate();

            return $affiliate->getUuid() . '@' . $campaign->getUuid();
        } catch (\Throwable $e) {
            return null;
        }
    }

}