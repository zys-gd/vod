<?php


namespace SubscriptionBundle\Service;


use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
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
        return $this->campaignRepository->findOneByCampaignToken($campaignToken);
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
}