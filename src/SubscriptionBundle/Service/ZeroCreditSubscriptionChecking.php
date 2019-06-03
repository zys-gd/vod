<?php


namespace SubscriptionBundle\Service;


use App\Domain\Entity\Campaign;
use SubscriptionBundle\Entity\SubscriptionPack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ZeroCreditSubscriptionChecking
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    public function __construct(CampaignExtractor $campaignExtractor)
    {
        $this->campaignExtractor = $campaignExtractor;
    }

    public function isAvailable(SessionInterface $session, SubscriptionPack $subscriptionPack): bool
    {
        if ($subscriptionPack->isZeroCreditSubAvailable()) {
            return true;
        }

        /** @var Campaign $campaign */
        $campaign = $this->campaignExtractor->getCampaignFromSession($session);
        if ($campaign && $campaign->isZeroCreditSubAvailable()) {
            return true;
        }

        return false;
    }

    public function isZeroCreditSubscription(): bool
    {

    }
}