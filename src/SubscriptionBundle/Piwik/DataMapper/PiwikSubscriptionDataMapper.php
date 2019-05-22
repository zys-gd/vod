<?php


namespace SubscriptionBundle\Piwik\DataMapper;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\User;
use LegacyBundle\Service\Exchanger;
use PiwikBundle\Service\DTO\EcommerceDTO;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\SubscriptionPackProvider;

class PiwikSubscriptionDataMapper
{
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var Exchanger
     */
    private $exchanger;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    public function __construct(SubscriptionPackProvider $subscriptionPackProvider,
        Exchanger $exchanger,
        CampaignExtractor $campaignExtractor)
    {
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->exchanger                = $exchanger;
        $this->campaignExtractor        = $campaignExtractor;
    }

    /**
     * Data for subscribe, renew, resubscribe
     *
     * @param User          $user
     * @param Subscription  $subscription
     * @param ProcessResult $bfResponse
     * @param string        $action
     *
     * @return EcommerceDTO
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     */
    public function getEcommerceDTO(User $user,
        Subscription $subscription,
        ProcessResult $bfResponse,
        string $action
    ): EcommerceDTO
    {
        $oSubPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        if ($oSubPack->getCarrier()->getBillingCarrierId() == ConstBillingCarrierId::HUTCH3_INDONESIA
            && $bfResponse->getStatus() === ProcessResult::STATUS_FAILED
            && $bfResponse->getError() === ProcessResult::ERROR_CANCELED) {
            return false;
        }

        $bfSuccess  = $bfResponse->getStatus() === 'successful';
        $bfId       = $bfResponse->getId();

        $subscriptionPackId = abs($oSubPack->getUuid());

        $eurPrice          = $this->exchanger->convert($oSubPack->getTierCurrency(), $oSubPack->getTierPrice());
        $subscriptionPrice = round($oSubPack->getTierPrice(), 2);
        $name              = $action . '-' . ($bfSuccess ? 'ok' : 'failed');

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
     * @param User      $user
     * @param string    $bfProvider
     * @param bool|null $conversionMode
     *
     * @return array
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     */
    public function getAdditionalData(User $user, string $bfProvider, bool $conversionMode = null)
    {
        $oSubPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

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