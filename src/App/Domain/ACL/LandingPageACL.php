<?php

namespace App\Domain\ACL;

use App\Domain\ACL\Accessors\VisitAccessorByCampaign;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Repository\CarrierRepository;
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
     * @param VisitConstraintByAffiliate $visitConstraintByAffiliate
     * @param VisitAccessorByCampaign    $visitAccessorByCampaign
     * @param CarrierRepository          $carrierRepository
     * @param CampaignRepository         $campaignRepository
     * @param SubscriptionLimiter        $subscriptionLimiter
     */
    public function __construct(
        VisitConstraintByAffiliate $visitConstraintByAffiliate,
        VisitAccessorByCampaign $visitAccessorByCampaign,
        CarrierRepository $carrierRepository,
        CampaignRepository $campaignRepository,
        SubscriptionLimiter $subscriptionLimiter
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
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($request->getSession());

        if (empty($ispDetectionData['carrier_id'])) {
            return true;
        }

        $carrier = $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);

        if (empty($carrier)) {
            return true;
        }

        if ($this->subscriptionLimiter->isSubscriptionLimitReached($request->getSession())) {
            return false;
        }

        $campaignToken = $request->get('cid', '');

        if (empty($campaignToken)) {
            return true;
        }

        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->findOneBy(['campaignToken' => $campaignToken]);

        if (!$this->visitAccessorByCampaign->canVisit($campaign, $carrier)) {
            return false;
        }

        if (!$this->visitConstraintByAffiliate->canVisit($campaign, $carrier)) {
            return false;
        }

        return true;
    }
}