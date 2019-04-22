<?php

namespace App\Domain\ACL;

use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Repository\CarrierRepository;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\ACL\Accessors\VisitAccessorByCampaign;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use SubscriptionBundle\Service\SubscriptionLimiter\SubscriptionLimiter;
use SubscriptionBundle\Service\SubscriptionLimiter\SubscriptionLimiterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LandingPageAccessResolver
 */
class LandingPageACL
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    /**
     * @var VisitConstraintByAffiliate
     */
    private $visitConstraintByAffiliate;

    /**
     * @var VisitAccessorByCampaign
     */
    private $visitAccessorByCampaign;
    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;

    /**
     * LandingPageAccessResolver constructor
     *
     * @param VisitConstraintByAffiliate   $visitConstraintByAffiliate
     * @param VisitAccessorByCampaign      $visitAccessorByCampaign
     * @param CarrierRepository            $carrierRepository
     * @param CampaignRepository           $campaignRepository
     * @param SubscriptionLimiterInterface $subscriptionLimiter
     */
    public function __construct(
        VisitConstraintByAffiliate $visitConstraintByAffiliate,
        VisitAccessorByCampaign $visitAccessorByCampaign,
        CarrierRepository $carrierRepository,
        CampaignRepository $campaignRepository,
        SubscriptionLimiterInterface $subscriptionLimiter
    )
    {
        $this->carrierRepository          = $carrierRepository;
        $this->campaignRepository         = $campaignRepository;
        $this->visitConstraintByAffiliate = $visitConstraintByAffiliate;
        $this->visitAccessorByCampaign    = $visitAccessorByCampaign;
        $this->subscriptionLimiter        = $subscriptionLimiter;
    }

    /**
     * @param Request $request
     *
     * @return string|null
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function canAccess(Request $request): ?string
    {
        $campaignToken = $request->get('cid', '');

        if (empty($campaignToken)) {
            return true;
        }

        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->findOneBy(['campaignToken' => $campaignToken]);

        if($campaign && $campaign->getIsPause()) {
            return false;
        }

        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($request->getSession());

        if (empty($ispDetectionData['carrier_id'])) {
            return true;
        }

        $carrier = $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);

        if (empty($carrier)) {
            return true;
        }

        if ($this->subscriptionLimiter->isLimitReached($request->getSession())) {
            return false;
        }

        if ($campaign && !$this->visitAccessorByCampaign->canVisit($campaign, $carrier)) {
            return false;
        }

        if ($campaign && !$this->visitConstraintByAffiliate->canVisit($campaign, $carrier)) {
            return false;
        }

        return true;
    }
}