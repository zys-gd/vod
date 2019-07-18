<?php


namespace SubscriptionBundle\Service\CAPTool\Limiter;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\CAPTool\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\CAPTool\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\CAPTool\DTO\LimiterData;
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

    public function mapFromSession(SessionInterface $session): LimiterData
    {
        $carrier = $this->extractCarrierFromSession($session);

        $constraint = $this->extractAffiliateConstraintFromSession($session);

        return new LimiterData($carrier, $constraint);
    }

    /**
     * @param SessionInterface $session
     *
     * @return CarrierInterface
     */
    private function extractCarrierFromSession(SessionInterface $session): CarrierInterface
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($session);
        $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);

        return $carrier;
    }

    /**
     * @param SessionInterface $session
     *
     * @return ConstraintByAffiliate|null
     */
    private function extractAffiliateConstraintFromSession(SessionInterface $session): ?ConstraintByAffiliate
    {
        try {
            $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($session);

            /** @var Campaign $campaign */
            $campaign = $this->campaignExtractor->getCampaignFromSession($session);

            /** @var Affiliate $affiliate */
            $affiliate = $campaign->getAffiliate();

            /** @var ConstraintByAffiliate $subscriptionConstraint */
            return $affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE, $billingCarrierId);

        } catch (\Throwable $e) {
            return null;
        }
    }
}