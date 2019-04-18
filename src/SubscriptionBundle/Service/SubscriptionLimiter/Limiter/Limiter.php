<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Limiter;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Limiter
{
    /**
     * @var LimiterPerformer
     */
    private $limiterPerformer;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    public function __construct(CampaignExtractor $campaignExtractor,
        SubscriptionExtractor $subscriptionExtractor,
        CarrierRepositoryInterface $carrierRepository,
        LimiterPerformer $limiterPerformer)
    {
        $this->campaignExtractor     = $campaignExtractor;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->carrierRepository     = $carrierRepository;
        $this->limiterPerformer      = $limiterPerformer;
    }

    /**
     * @param CarrierLimiterData|null   $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     */
    public function decrProcessingSlots(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData): void
    {
        if ($carrierLimiterData) {
            $this->limiterPerformer->decrCarrierProcessingSlotsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId());
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->decrAffiliateProcessingSlotsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid());
        }
    }

    /**
     * @param CarrierLimiterData|null   $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     */
    public function incrProcessingSlots(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData)
    {
        if ($carrierLimiterData) {
            $this->limiterPerformer->incrCarrierProcessingSlotsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId());
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->incrAffiliateProcessingSlotsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid());
        }
    }

    /**
     * @param CarrierLimiterData|null   $carrierLimiterData
     * @param AffiliateLimiterData|null $affiliateLimiterData
     */
    public function decrSubscriptionSlots(?CarrierLimiterData $carrierLimiterData,
        ?AffiliateLimiterData $affiliateLimiterData)
    {
        if ($carrierLimiterData) {
            $this->limiterPerformer->decrCarrierSubscriptionSlotsWithLock($carrierLimiterData->getCarrier()->getBillingCarrierId());
        }
        if ($affiliateLimiterData) {
            $this->limiterPerformer->decrAffiliateSubscriptionSlotsWithLock($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid());
        }
    }

    /**
     * @param CarrierLimiterData $carrierLimiterData
     *
     * @return mixed
     */
    public function getCarrierProcessingSlots(CarrierLimiterData $carrierLimiterData): int
    {
        return $this->limiterPerformer->getCarrierSlots($carrierLimiterData->getCarrier()->getBillingCarrierId())[LimiterDataMapper::PROCESSING_SLOTS];
    }

    /**
     * @param AffiliateLimiterData $affiliateLimiterData
     *
     * @return int
     */
    public function getAffiliateProcessingSlots(AffiliateLimiterData $affiliateLimiterData): int
    {
        return $this->limiterPerformer->getCarrierAffiliateConstraintSlots($affiliateLimiterData->getBillingCarrierId(), $affiliateLimiterData->getAffiliate()->getUuid(), $affiliateLimiterData->getConstraintByAffiliate()->getUuid())[LimiterDataMapper::PROCESSING_SLOTS];
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