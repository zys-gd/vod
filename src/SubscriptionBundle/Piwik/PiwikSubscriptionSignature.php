<?php


namespace SubscriptionBundle\Piwik;


use App\Domain\Entity\Campaign;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\ZeroCreditSubscriptionChecking;

class PiwikSubscriptionSignature
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $creditSubscriptionChecking;

    /**
     * PiwikSubscriptionSignature constructor.
     *
     * @param CampaignExtractor              $campaignExtractor
     * @param ZeroCreditSubscriptionChecking $creditSubscriptionChecking
     */
    public function __construct(CampaignExtractor $campaignExtractor, ZeroCreditSubscriptionChecking $creditSubscriptionChecking)
    {
        $this->campaignExtractor = $campaignExtractor;
        $this->creditSubscriptionChecking = $creditSubscriptionChecking;
    }

    /**
     * @param string           $action
     * @param bool             $resultStatus
     * @param SubscriptionPack $subscriptionPack
     * @param Campaign|null    $campaign
     *
     * @return string
     */
    public function get(string $action, bool $resultStatus, SubscriptionPack $subscriptionPack, Campaign $campaign = null): string
    {
        $additionalMark = $this->getAdditionalMark($subscriptionPack, $campaign);

        $textResultStatus = $resultStatus ? 'ok' : 'failed';

        return $action . $additionalMark . '-' . $textResultStatus;
    }

    /**
     * @param SubscriptionPack $subscriptionPack
     * @param Campaign|null    $campaign
     *
     * @return string
     */
    private function getAdditionalMark(SubscriptionPack $subscriptionPack, Campaign $campaign = null)
    {
        if($subscriptionPack->isFirstSubscriptionPeriodIsFree()) {
            return '-freetrial';
        }

        if($subscriptionPack->isZeroCreditSubAvailable()){
            return '-zerocredit';
        }

        if($campaign && $campaign->isZeroCreditSubAvailable()){
            return '-zerocredit';
        }

        return '-paid';
    }
}