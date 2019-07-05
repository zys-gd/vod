<?php

namespace SubscriptionBundle\Service;

use App\Domain\Entity\Campaign;
use SubscriptionBundle\Entity\SubscriptionPack;
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
     * ZeroCreditSubscriptionChecking constructor
     *
     * @param CampaignExtractor $campaignExtractor
     * @param SessionInterface $session
     */
    public function __construct(CampaignExtractor $campaignExtractor, SessionInterface $session)
    {
        $this->campaignExtractor = $campaignExtractor;
        $this->session = $session;
    }

    /**
     * @param SubscriptionPack $subscriptionPack
     *
     * @return bool
     */
    public function isAvailable(SubscriptionPack $subscriptionPack): bool
    {
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