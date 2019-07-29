<?php


namespace SubscriptionBundle\Piwik;


use App\Domain\Entity\Campaign;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking;

class PiwikSubscriptionSignature
{
    /**
     * @var \SubscriptionBundle\Affiliate\Service\CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var \SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking
     */
    private $creditSubscriptionChecking;

    /**
     * PiwikSubscriptionSignature constructor.
     *
     * @param \SubscriptionBundle\Affiliate\Service\CampaignExtractor                          $campaignExtractor
     * @param \SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking $creditSubscriptionChecking
     */
    public function __construct(CampaignExtractor $campaignExtractor,
        ZeroCreditSubscriptionChecking $creditSubscriptionChecking)
    {
        $this->campaignExtractor          = $campaignExtractor;
        $this->creditSubscriptionChecking = $creditSubscriptionChecking;
    }

    /**
     * @param string           $action
     * @param bool             $resultStatus
     * @param int|null         $chargePaid
     * @param SubscriptionPack $subscriptionPack
     * @param Campaign|null    $campaign
     *
     * @return string
     */
    public function get(string $action,
        bool $resultStatus,
        ?int $chargePaid,
        SubscriptionPack $subscriptionPack,
        Campaign $campaign = null): string
    {
        $additionalMark = $this->getAdditionalMark($chargePaid, $subscriptionPack, $campaign);

        $textResultStatus = $resultStatus ? 'ok' : 'failed';

        return $action . $additionalMark . '-' . $textResultStatus;
    }

    /**
     * @param int|null         $chargePaid
     * @param SubscriptionPack $subscriptionPack
     * @param Campaign|null    $campaign
     *
     * @return string
     */
    private function getAdditionalMark(?int $chargePaid, SubscriptionPack $subscriptionPack, Campaign $campaign = null)
    {
        if ($subscriptionPack->isFirstSubscriptionPeriodIsFree()) {
            return '-freetrial';
        }

        if ($subscriptionPack->isZeroCreditSubAvailable() && $chargePaid === 0) {
            return '-zerocredit';
        }

        if ($campaign && $campaign->isZeroCreditSubAvailable() && $chargePaid === 0) {
            return '-zerocredit';
        }

        return '-paid';
    }
}