<?php


namespace SubscriptionBundle\Piwik\DataMapper;

use LegacyBundle\Service\Exchanger;
use PiwikBundle\Service\DTO\EcommerceDTO;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\CampaignExtractor;

class PiwikSubscriptionDataMapper
{
    /**
     * @var Exchanger
     */
    private $exchanger;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    public function __construct(Exchanger $exchanger, CampaignExtractor $campaignExtractor)
    {
        $this->exchanger         = $exchanger;
        $this->campaignExtractor = $campaignExtractor;
    }

    /**
     * Data for subscribe, renew, resubscribe
     *
     * @param Subscription  $subscription
     * @param ProcessResult $bfResponse
     * @param string        $action
     * @param bool          $resultStatus
     *
     * @return EcommerceDTO
     */
    public function getEcommerceDTO(Subscription $subscription,
        ProcessResult $bfResponse,
        string $action,
        bool $resultStatus
    ): EcommerceDTO
    {
        $oSubPack = $subscription->getSubscriptionPack();
        $bfId     = $bfResponse->getId();

        $subscriptionPackId = abs($oSubPack->getUuid());

        $eurPrice          = $this->exchanger->convert($oSubPack->getTierCurrency(), $oSubPack->getTierPrice());
        $subscriptionPrice = round($oSubPack->getTierPrice(), 2);

        $campaign   = $this->campaignExtractor->getCampaignForSubscription($subscription);
        $zerocredit = '';
        if (
            $oSubPack->isZeroCreditSubAvailable()
            ||
            ($campaign && $campaign->isZeroCreditSubAvailable())
        ) {
            $zerocredit = '-zerocredit';
        }

        $name = $action . $zerocredit . '-' . ($resultStatus ? 'ok' : 'failed');

        $orderIdPieces = [
            $name,
            $subscription->getUuid(),
            $subscriptionPackId,
            $bfId,
            $subscriptionPrice,
        ];

        $orderId = implode('-', $orderIdPieces);

        return new EcommerceDTO($orderId, $eurPrice, $name . '-' . $subscriptionPackId, $action);
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

    /**
     * @param Subscription $subscription
     * @param string       $bfProvider
     * @param bool|null    $conversionMode
     *
     * @return array
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     */
    public function getAdditionalData(Subscription $subscription, string $bfProvider, bool $conversionMode = null)
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
}