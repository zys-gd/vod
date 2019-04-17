<?php

namespace App\Domain\ACL;

use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Repository\CarrierRepository;
use App\Domain\ACL\Accessors\VisitConstraintByAffiliate;
use App\Domain\ACL\Accessors\VisitAccessorByCampaign;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\SubscriptionLimiter;
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
     * @param VisitConstraintByAffiliate      $visitConstraintByAffiliate
     * @param VisitAccessorByCampaign         $visitAccessorByCampaign
     * @param CarrierRepository               $carrierRepository
     * @param CampaignRepository              $campaignRepository
     * @param SubscriptionLimiter             $subscriptionLimiter
     */
    public function __construct(
        VisitConstraintByAffiliate $visitConstraintByAffiliate,
        VisitAccessorByCampaign $visitAccessorByCampaign,
        CarrierRepository $carrierRepository,
        CampaignRepository $campaignRepository,
        SubscriptionLimiter $subscriptionLimiter
    )
    {
        $this->carrierRepository               = $carrierRepository;
        $this->campaignRepository              = $campaignRepository;
        $this->visitConstraintByAffiliate      = $visitConstraintByAffiliate;
        $this->visitAccessorByCampaign         = $visitAccessorByCampaign;
        $this->subscriptionLimiter             = $subscriptionLimiter;
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

        $carrierLimiterData = new CarrierLimiterData($carrier);
        try{
            $carrierLimiterData->setSubscriptionConstraint($campaign->getAffiliate()->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE, $carrier));
            $carrierLimiterData->setAffiliate($campaign->getAffiliate());
        } catch (\Throwable $e) { }
        if ($this->subscriptionLimiter->isLimitReached($carrierLimiterData)) {
            return false;
        }

        return true;
    }
}