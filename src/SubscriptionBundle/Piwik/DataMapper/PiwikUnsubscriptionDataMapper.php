<?php


namespace SubscriptionBundle\Piwik\DataMapper;

use LegacyBundle\Service\Exchanger;
use PiwikBundle\Service\DTO\EcommerceDTO;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\PiwikSubscriptionSignature;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;

class PiwikUnsubscriptionDataMapper
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var Exchanger
     */
    private $exchanger;
    /**
     * @var PiwikSubscriptionSignature
     */
    private $piwikSubscriptionSignature;

    public function __construct(Exchanger $exchanger,
        CampaignExtractor $campaignExtractor,
        PiwikSubscriptionSignature $piwikSubscriptionSignature)
    {
        $this->exchanger                  = $exchanger;
        $this->campaignExtractor          = $campaignExtractor;
        $this->piwikSubscriptionSignature = $piwikSubscriptionSignature;
    }

    /**
     * @param int          $responseId
     * @param int          $chargePaid
     * @param bool         $resultStatus
     * @param Subscription $subscription
     * @param string       $action
     *
     * @return bool|EcommerceDTO
     */
    public function getEcommerceDTO(
        ?int $responseId,
        ?int $chargePaid,
        bool $resultStatus,
        Subscription $subscription,
        string $action
    ): EcommerceDTO
    {

        $oSubPack = $subscription->getSubscriptionPack();

        $subscriptionPackId = abs($oSubPack->getUuid());
        $eurPrice           = $this->exchanger->convert($oSubPack->getTierCurrency(), $oSubPack->getTierPrice());
        $subscriptionPrice  = round($oSubPack->getTierPrice(), 2);

        $campaign = $this->campaignExtractor->getCampaignForSubscription($subscription);
        $name     = $this->piwikSubscriptionSignature->get($action, $resultStatus, $chargePaid, $oSubPack, $campaign);

        $orderIdPieces = [
            $name,
            $subscription->getUuid(),
            $subscriptionPackId,
            $responseId ?: 'N' . rand(1000, 9999),
            $subscriptionPrice,
            mt_rand(0, 9999)
        ];

        $orderId = implode('-', $orderIdPieces);

        return new EcommerceDTO($orderId, $eurPrice, $name . '-' . $subscriptionPackId, $action);
    }

    /**
     * @param Subscription $subscription
     * @param string|null  $bfProvider
     * @param bool|null    $conversionMode
     *
     * @return array
     */
    public function getAdditionalData(Subscription $subscription,
        string $bfProvider = null,
        bool $conversionMode = null)
    {
        $oSubPack = $subscription->getSubscriptionPack();

        $additionData = [
            'currency' => $oSubPack->getFinalCurrency(),
            'provider' => $bfProvider
        ];

        if ($conversionMode) {
            $additionData['conversion_mode'] = $conversionMode;
        }

        return $additionData;
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