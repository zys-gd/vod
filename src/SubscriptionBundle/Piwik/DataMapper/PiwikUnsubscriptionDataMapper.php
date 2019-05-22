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

class PiwikUnsubscriptionDataMapper
{
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var Exchanger
     */
    private $exchanger;

    public function __construct(SubscriptionPackProvider $subscriptionPackProvider,
        Exchanger $exchanger,
        CampaignExtractor $campaignExtractor)
    {
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->exchanger                = $exchanger;
        $this->campaignExtractor        = $campaignExtractor;
    }

    /**
     * @param User               $user
     * @param Subscription       $subscription
     * @param ProcessResult|null $bfResponse
     * @param string             $action
     *
     * @return bool|EcommerceDTO
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     */
    public function getEcommerceDTO(
        User $user,
        Subscription $subscription,
        ProcessResult $bfResponse,
        string $action
    )
    {
        $bfId      = $bfProvider = $oSubPack = false;
        $bfSuccess = true;

        $oSubPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        $bfWrong = $bfResponse
            && $bfResponse->getError() != ProcessResult::ERROR_BATCH_LIMIT_EXCEEDED
            && $bfResponse->getError() != ProcessResult::ERROR_USER_TIMEOUT
            && !($bfResponse->getError() == ProcessResult::ERROR_CANCELED && $oSubPack->getCarrier()->getBillingCarrierId() == ConstBillingCarrierId::ROBI_BANGLADESH)
            &&
            (
                $bfResponse->getType() !== 'unsubscribe'
                || !in_array($bfResponse->getStatus(), ['successful', 'failed', 'ok'])
            );

        if (!$oSubPack || $bfWrong) {
            return false;
        }

        if ($bfResponse) {
            $bfSuccess = $bfResponse->getError() == ProcessResult::ERROR_BATCH_LIMIT_EXCEEDED ||
            $bfResponse->getError() == ProcessResult::ERROR_USER_TIMEOUT ||
            ($bfResponse->getError() == ProcessResult::ERROR_CANCELED && $oSubPack->getCarrier()->getBillingCarrierId() == ConstBillingCarrierId::ROBI_BANGLADESH)
                ? true : ($bfResponse->getStatus() === 'successful' || $bfResponse->getStatus() === 'ok');

            $bfId = $bfResponse->getId();
        }

        $subscriptionPackId = abs($oSubPack->getUuid());
        $eurPrice           = $this->exchanger->convert($oSubPack->getTierCurrency(), $oSubPack->getTierPrice());
        $subscriptionPrice  = round($oSubPack->getTierPrice(), 2);

        $name          = $action . '-' . ($bfSuccess ? 'ok' : 'failed');
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