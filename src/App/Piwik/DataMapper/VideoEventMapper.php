<?php


namespace App\Piwik\DataMapper;


use App\Domain\Entity\UploadedVideo;
use PiwikBundle\Service\DTO\EcommerceDTO;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\Piwik\DataMapper\OrderInformationMapper;
use SubscriptionBundle\Piwik\DataMapper\UserInformationMapper;
use SubscriptionBundle\Piwik\DTO\ConversionEvent;
use SubscriptionBundle\Piwik\DTO\OrderInformation;

class VideoEventMapper
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var UserInformationMapper
     */
    private $userInformationMapper;
    /**
     * @var OrderInformationMapper
     */
    private $orderInformationMapper;

    public function __construct(CampaignExtractor $campaignExtractor, UserInformationMapper $userInformationMapper, OrderInformationMapper $orderInformationMapper)
    {
        $this->campaignExtractor      = $campaignExtractor;
        $this->userInformationMapper  = $userInformationMapper;
        $this->orderInformationMapper = $orderInformationMapper;
    }

    public function map(Subscription $subscription, UploadedVideo $uploadedVideo): ConversionEvent
    {

        $subscriptionPack = $subscription->getSubscriptionPack();
        $orderInformation = $this->mapOrderInformation($subscription, $uploadedVideo, $subscriptionPack);
        $userInfo         = $this->userInformationMapper->mapUserInformation(
            $subscription->getUser(),
            $subscription,
            0
        );
        $additionalData   = $this->getAdditionalData($uploadedVideo);

        return new ConversionEvent($userInfo, $orderInformation, $additionalData);
    }

    /**
     * /**
     * @param UploadedVideo $uploadedVideo
     * @return array
     */
    private function getAdditionalData(UploadedVideo $uploadedVideo): array
    {
        return [
            15 => ['video_name', $uploadedVideo->getTitle()],
            16 => ['video_uuid', $uploadedVideo->getUuid()]
        ];
    }

    /**
     * @param Subscription                                $subscription
     * @param UploadedVideo                               $uploadedVideo
     * @param \SubscriptionBundle\Entity\SubscriptionPack $subscriptionPack
     * @return OrderInformation
     */
    private function mapOrderInformation(
        Subscription $subscription,
        UploadedVideo $uploadedVideo,
        \SubscriptionBundle\Entity\SubscriptionPack $subscriptionPack
    ): OrderInformation
    {
        $orderIdPieces = [
            'playing-video',
            $subscription->getUuid(),
            $subscriptionPack->getUuid(),
            $uploadedVideo->getUuid(),
            'N' . rand(1000, 9999),
        ];
        $orderId       = implode('-', $orderIdPieces);
        $alias         = sprintf('playing-video-%s', $uploadedVideo->getUuid());

        $orderInformation = new OrderInformation(
            $orderId,
            0.01,
            $alias,
            'game',
            $subscriptionPack->getTierCurrency()
        );
        return $orderInformation;
    }
}