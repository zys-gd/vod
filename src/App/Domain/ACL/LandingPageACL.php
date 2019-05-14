<?php

namespace App\Domain\ACL;

use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Repository\CarrierRepository;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\ACL\Accessors\VisitAccessorByCampaign;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use SubscriptionBundle\Service\CapConstraint\SubscriptionConstraintByCarrier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @var SessionInterface
     */
    private $session;

    /**
     * @var VisitConstraintByAffiliate
     */
    private $visitConstraintByAffiliate;

    /**
     * @var VisitAccessorByCampaign
     */
    private $visitAccessorByCampaign;

    /**
     * @var SubscriptionConstraintByCarrier
     */
    private $subscriptionConstraintByCarrier;

    /**
     * LandingPageAccessResolver constructor
     *
     * @param VisitConstraintByAffiliate $visitConstraintByAffiliate
     * @param VisitAccessorByCampaign $visitAccessorByCampaign
     * @param SubscriptionConstraintByCarrier $subscriptionConstraintByCarrier
     * @param CarrierRepository $carrierRepository
     * @param CampaignRepository $campaignRepository
     * @param SessionInterface $session
     */
    public function __construct(
        VisitConstraintByAffiliate $visitConstraintByAffiliate,
        VisitAccessorByCampaign $visitAccessorByCampaign,
        SubscriptionConstraintByCarrier $subscriptionConstraintByCarrier,
        CarrierRepository $carrierRepository,
        CampaignRepository $campaignRepository,
        SessionInterface $session
    ) {
        $this->carrierRepository = $carrierRepository;
        $this->campaignRepository = $campaignRepository;
        $this->session = $session;
        $this->visitConstraintByAffiliate = $visitConstraintByAffiliate;
        $this->visitAccessorByCampaign = $visitAccessorByCampaign;
        $this->subscriptionConstraintByCarrier = $subscriptionConstraintByCarrier;
    }

    /**
     * @param Request $request
     *
     * @return string|null
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function canAccess(Request $request): ?string
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);

        if (empty($ispDetectionData['carrier_id'])) {
            return true;
        }

        $carrier =  $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);

        if (empty($carrier)) {
            return true;
        }

        if ($this->subscriptionConstraintByCarrier->isSubscriptionLimitReached($carrier)) {
            return false;
        }

        $campaignToken = $request->get('cid', '');

        if (empty($campaignToken)) {
            return true;
        }

        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->findOneBy(['campaignToken' => $campaignToken]);

        if ($campaign && !$this->visitAccessorByCampaign->canVisit($campaign, $carrier)) {
            return false;
        }

        if ($campaign && !$this->visitConstraintByAffiliate->canVisit($campaign, $carrier)) {
            return false;
        }

        return true;
    }
}