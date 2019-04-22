<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LimiterDataMapper
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    public function __construct(CampaignExtractor $campaignExtractor, CarrierRepositoryInterface $carrierRepository)
    {
        $this->campaignExtractor = $campaignExtractor;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * @param SessionInterface $session
     *
     * @return CarrierLimiterData|null
     */
    public function createCarrierLimiterDataFromSession(SessionInterface $session): ?CarrierLimiterData
    {
        $ispData          = IdentificationFlowDataExtractor::extractIspDetectionData($session);
        $billingCarrierId = $ispData['carrier_id'];
        $carrier          = $this->carrierRepository->findOneByBillingId($billingCarrierId);

        if ($carrier->getNumberOfAllowedSubscriptionsByConstraint() > 0) {
            $carrierLimiterData = new CarrierLimiterData($carrier);

            return $carrierLimiterData;
        }

        return null;
    }

    /**
     * @param SessionInterface $session
     *
     * @return AffiliateLimiterData|null
     */
    public function createAffiliateLimiterDataFromSession(SessionInterface $session): ?AffiliateLimiterData
    {
        try {
            $ispData          = IdentificationFlowDataExtractor::extractIspDetectionData($session);
            $billingCarrierId = $ispData['carrier_id'];

            /** @var Campaign $campaign */
            $campaign = $this->campaignExtractor->getCampaignFromSession($session);

            /** @var Affiliate $affiliate */
            $affiliate = $campaign->getAffiliate();

            /** @var ConstraintByAffiliate $subscriptionConstraint */
            $subscriptionConstraint = $affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE, $billingCarrierId);

            $affiliateLimiterData = new AffiliateLimiterData($affiliate, $subscriptionConstraint, $billingCarrierId);

            return $affiliateLimiterData;
        } catch (\Throwable $e) {
            return null;
        }
    }
}