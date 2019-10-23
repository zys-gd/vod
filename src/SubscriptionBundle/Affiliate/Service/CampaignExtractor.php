<?php


namespace SubscriptionBundle\Affiliate\Service;


use ExtrasBundle\Cache\ArrayCache\ArrayCacheService;
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
    /**
     * @var ArrayCacheService
     */
    private $arrayCacheService;

    /**
     * CampaignExtractor constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     * @param ArrayCacheService           $arrayCacheService
     */
    public function __construct(CampaignRepositoryInterface $campaignRepository, ArrayCacheService $arrayCacheService)
    {
        $this->campaignRepository = $campaignRepository;
        $this->arrayCacheService  = $arrayCacheService;
    }

    /**
     * @param SessionInterface $session
     *
     * @return CampaignInterface|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCampaignFromSession(SessionInterface $session): ?CampaignInterface
    {
        $campaignToken = AffiliateVisitSaver::extractCampaignToken($session);

        if (!$campaignToken) {
            return null;
        }

        $key = sprintf('%s_%s', $campaignToken, 'campaign');

        if ($this->arrayCacheService->hasCache($key)) {
            $campaign = $this->arrayCacheService->getValue($key);
        } else {
            $campaign = $this->campaignRepository->findOneByCampaignToken($campaignToken);
            $this->arrayCacheService->saveCache($key, $campaign);
        }

        return $campaign;
    }

    /**
     * @param SessionInterface $session
     *
     * @return AffiliateInterface|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function extractAffiliateFromCampaign(SessionInterface $session): ?AffiliateInterface
    {
        $campaign = $this->getCampaignFromSession($session);
        return $campaign ? $campaign->getAffiliate() : null;
    }

    public function getCampaignForSubscription(Subscription $subscription): ?CampaignInterface
    {
        $affiliateData = $subscription->getAffiliateToken();
        $campaignToken = $affiliateData['cid'] ?? null;

        return $campaignToken ?
            $this->campaignRepository->findOneByCampaignToken($campaignToken)
            : null;
    }
}