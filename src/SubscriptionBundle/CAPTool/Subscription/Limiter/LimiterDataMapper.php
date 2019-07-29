<?php


namespace SubscriptionBundle\CAPTool\Subscription\Limiter;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\CAPTool\Subscription\DTO\AffiliateLimiterData;
use SubscriptionBundle\CAPTool\Subscription\DTO\CarrierLimiterData;
use SubscriptionBundle\CAPTool\Subscription\DTO\LimiterData;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LimiterDataMapper
{
    /**
     * @var \SubscriptionBundle\Affiliate\Service\CampaignExtractor
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