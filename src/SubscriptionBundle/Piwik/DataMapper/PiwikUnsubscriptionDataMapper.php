<?php


namespace SubscriptionBundle\Piwik\DataMapper;

use LegacyBundle\Service\Exchanger;
use PiwikBundle\Service\DTO\EcommerceDTO;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\CampaignExtractor;

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

    public function __construct(Exchanger $exchanger, CampaignExtractor $campaignExtractor)
    {
        $this->exchanger         = $exchanger;
        $this->campaignExtractor = $campaignExtractor;
    }

    /**
     * @param Subscription       $subscription
     * @param ProcessResult|null $bfResponse
     * @param string             $action
     * @param bool               $resultStatus
     *
     * @return bool|EcommerceDTO
     */
    public function getEcommerceDTO(
        Subscription $subscription,
        ProcessResult $bfResponse,
        string $action,
        bool $resultStatus
    ): EcommerceDTO
    {
        $bfId = $bfProvider = $oSubPack = false;

        $oSubPack = $subscription->getSubscriptionPack();

        $bfId = $bfResponse->getId();

        $subscriptionPackId = abs($oSubPack->getUuid());
        $eurPrice           = $this->exchanger->convert($oSubPack->getTierCurrency(), $oSubPack->getTierPrice());
        $subscriptionPrice  = round($oSubPack->getTierPrice(), 2);

        $name          = $action . '-' . ($resultStatus ? 'ok' : 'failed');
        $orderIdPieces = [
            $name,
            $subscription->getUuid(),
            $subscriptionPackId,
            $bfId ?: 'N' . rand(1000, 9999),
            $subscriptionPrice,
            mt_rand(0, 9999)
        ];

        $orderId = implode('-', $orderIdPieces);

        return new EcommerceDTO($orderId, $eurPrice, $name . '-' . $subscriptionPackId, $action);
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