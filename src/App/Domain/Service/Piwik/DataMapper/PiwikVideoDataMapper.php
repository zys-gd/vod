<?php


namespace App\Domain\Service\Piwik\DataMapper;


use App\Domain\Entity\UploadedVideo;
use PiwikBundle\Service\DTO\EcommerceDTO;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;

class PiwikVideoDataMapper
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    public function __construct(CampaignExtractor $campaignExtractor)
    {
        $this->campaignExtractor = $campaignExtractor;
    }

    /**
     * @param Subscription  $subscription
     * @param UploadedVideo $uploadedVideo
     *
     * @return EcommerceDTO
     */
    public function getEcommerceDTO(Subscription $subscription, UploadedVideo $uploadedVideo): EcommerceDTO
    {
        $oSubPack = $subscription->getSubscriptionPack();

        $subscriptionId = $subscription ? $subscription->getUuid() : 0;

        $subscriptionPlanId = $subscription ? abs($oSubPack->getUuid()) : 0;

        $type    = 'playing-video';
        $prodSku = 'playing-video-' . $uploadedVideo->getUuid();

        $orderIdPieces = [
            $type,
            $subscriptionId,
            $subscriptionPlanId,
            $uploadedVideo->getUuid(),
            'N' . rand(1000, 9999),
        ];
        $orderId       = implode('-', $orderIdPieces);
        return new EcommerceDTO($orderId, 0.01, $prodSku, 'game');
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
     * @param UploadedVideo $uploadedVideo
     * @param bool|null     $conversionMode
     *
     * @return array
     */
    public function getAdditionalData(UploadedVideo $uploadedVideo, bool $conversionMode = null): array
    {
        $additionData = [
            'video_name' => $uploadedVideo->getTitle(),
            'video_uuid' => $uploadedVideo->getUuid()
        ];

        if ($conversionMode) {
            $additionData['conversion_mode'] = $conversionMode;
        }

        return $additionData;
    }
}