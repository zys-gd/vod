<?php


namespace App\Domain\Service\Piwik\DataMapper;


use App\Domain\Entity\Game;
use PiwikBundle\Service\DTO\EcommerceDTO;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;

class PiwikGameDataMapper
{
    /**
     * @var \SubscriptionBundle\Affiliate\Service\CampaignExtractor
     */
    private $campaignExtractor;

    public function __construct(CampaignExtractor $campaignExtractor)
    {
        $this->campaignExtractor = $campaignExtractor;
    }

    public function getEcommerceDTO(Subscription $subscription, Game $game): EcommerceDTO
    {
        $oSubPack = $subscription->getSubscriptionPack();

        $subscriptionId = $subscription ? $subscription->getUuid() : 0;

        $subscriptionPlanId = $subscription ? abs($oSubPack->getUuid()) : 0;

        $type    = 'download-ok';
        $prodSku = 'download-' . $game->getUuid();

        $orderIdPieces = [
            $type,
            $subscriptionId,
            $subscriptionPlanId,
            $game->getUuid(),
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
     * @param Game      $game
     * @param bool|null $conversionMode
     *
     * @return array
     */
    public function getAdditionalData(Game $game, bool $conversionMode = null): array
    {
        $additionData = [
            'game_name' => $game->getName(),
            'game_uuid' => $game->getUuid()
        ];

        if ($conversionMode) {
            $additionData['conversion_mode'] = $conversionMode;
        }

        return $additionData;
    }
}