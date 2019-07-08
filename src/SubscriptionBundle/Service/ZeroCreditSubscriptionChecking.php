<?php

namespace SubscriptionBundle\Service;

use App\Domain\Entity\Campaign;
use IdentificationBundle\Entity\CarrierInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ZeroCreditSubscriptionChecking
 */
class ZeroCreditSubscriptionChecking
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;

    /**
     * ZeroCreditSubscriptionChecking constructor
     *
     * @param CampaignExtractor $campaignExtractor
     * @param SessionInterface $session
     * @param SubscriptionPackProvider $subscriptionPackProvider
     */
    public function __construct(
        CampaignExtractor $campaignExtractor,
        SessionInterface $session,
        SubscriptionPackProvider $subscriptionPackProvider
    ) {
        $this->campaignExtractor = $campaignExtractor;
        $this->session = $session;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function isAvailable(CarrierInterface $carrier): bool
    {
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPackFromCarrier($carrier);

        if ($subscriptionPack->isZeroCreditSubAvailable()) {
            /** @var Campaign $campaign */
            $campaign = $this->campaignExtractor->getCampaignFromSession($this->session);
            if ($campaign && !$campaign->isZeroCreditSubAvailable()) {
                return false;
            }

            return true;
        }

        return false;
    }
}