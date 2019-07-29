<?php


namespace SubscriptionBundle\Affiliate\Service;


use App\Domain\Entity\Campaign;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CampaignExtractor
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    public function __construct(CampaignRepositoryInterface $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @param SessionInterface $session
     *
     * @return CampaignInterface|null
     */
    public function getCampaignFromSession(SessionInterface $session): ?CampaignInterface
    {
        $campaignToken = AffiliateVisitSaver::extractCampaignToken($session);
        return $campaignToken ?
            $this->campaignRepository->findOneByCampaignToken($campaignToken)
            : null;
    }

    /**
     * @param SessionInterface $session
     *
     * @return AffiliateInterface|null
     */
    public function extractAffiliateFromCampaign(SessionInterface $session): ?AffiliateInterface
    {
        $campaign = $this->getCampaignFromSession($session);
        return $campaign ? $campaign->getAffiliate() : null;
    }

    public function getCampaignForSubscription(Subscription $subscription): ?Campaign
    {
        $affiliateData = $subscription->getAffiliateToken();
        $campaignToken = $affiliateData['cid'] ?? null;

        return $campaignToken ?
            $this->campaignRepository->findOneByCampaignToken($campaignToken)
            : null;
    }
}