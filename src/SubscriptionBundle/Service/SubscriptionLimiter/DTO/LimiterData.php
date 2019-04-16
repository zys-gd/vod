<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\DTO;


use App\Domain\Entity\Affiliate;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class LimiterData
{

    /**
     * @var CarrierInterface
     */
    private $carrier;
    /**
     * @var Affiliate
     */
    private $affiliate;
    /**
     * @var ConstraintByAffiliate
     */
    private $subscriptionConstraint;
    /**
     * @var int
     */
    private $carrierProcessingSlots;
    /**
     * @var int
     */
    private $carrierOpenSubscriptionSlots;
    /**
     * @var int
     */
    private $affiliateProcessingSlots;
    /**
     * @var int
     */
    private $affiliateOpenSubscriptionSlots;

    /**
     * LimiterData constructor.
     *
     * @param CarrierInterface           $carrier
     * @param Affiliate|null             $affiliate
     * @param ConstraintByAffiliate|null $subscriptionConstraint
     * @param int|null                   $carrierProcessingSlots
     * @param int|null                   $carrierOpenSubscriptionSlots
     * @param int|null                   $affiliateProcessingSlots
     * @param int|null                   $affiliateOpenSubscriptionSlots
     */
    public function __construct(CarrierInterface $carrier,
        Affiliate $affiliate = null,
        ConstraintByAffiliate $subscriptionConstraint = null,
        int $carrierProcessingSlots = null,
        int $carrierOpenSubscriptionSlots = null,
        int $affiliateProcessingSlots = null,
        int $affiliateOpenSubscriptionSlots = null)
    {
        $this->carrier = $carrier;
        $this->affiliate = $affiliate;
        $this->subscriptionConstraint = $subscriptionConstraint;
        $this->carrierProcessingSlots = $carrierProcessingSlots;
        $this->carrierOpenSubscriptionSlots = $carrierOpenSubscriptionSlots;
        $this->affiliateProcessingSlots = $affiliateProcessingSlots;
        $this->affiliateOpenSubscriptionSlots = $affiliateOpenSubscriptionSlots;
    }

    /**
     * @return CarrierInterface
     */
    public function getCarrier(): ?CarrierInterface
    {
        return $this->carrier;
    }

    /**
     * @return Affiliate
     */
    public function getAffiliate(): ?Affiliate
    {
        return $this->affiliate;
    }

    /**
     * @param Affiliate $affiliate
     */
    public function setAffiliate(Affiliate $affiliate): void
    {
        $this->affiliate = $affiliate;
    }

    /**
     * @return ConstraintByAffiliate
     */
    public function getSubscriptionConstraint(): ?ConstraintByAffiliate
    {
        return $this->subscriptionConstraint;
    }

    /**
     * @param ConstraintByAffiliate $subscriptionConstraint
     */
    public function setSubscriptionConstraint(ConstraintByAffiliate $subscriptionConstraint): void
    {
        $this->subscriptionConstraint = $subscriptionConstraint;
    }

    /**
     * @return int
     */
    public function getCarrierProcessingSlots(): ?int
    {
        return $this->carrierProcessingSlots;
    }

    /**
     * @param int $carrierProcessingSlots
     */
    public function setCarrierProcessingSlots(int $carrierProcessingSlots): void
    {
        $this->carrierProcessingSlots = $carrierProcessingSlots;
    }

    /**
     * @return int
     */
    public function getCarrierOpenSubscriptionSlots(): ?int
    {
        return $this->carrierOpenSubscriptionSlots;
    }

    /**
     * @param int $carrierOpenSubscriptionSlots
     */
    public function setCarrierOpenSubscriptionSlots(int $carrierOpenSubscriptionSlots): void
    {
        $this->carrierOpenSubscriptionSlots = $carrierOpenSubscriptionSlots;
    }

    /**
     * @return int
     */
    public function getAffiliateProcessingSlots(): ?int
    {
        return $this->affiliateProcessingSlots;
    }

    /**
     * @param int $affiliateProcessingSlots
     */
    public function setAffiliateProcessingSlots(int $affiliateProcessingSlots): void
    {
        $this->affiliateProcessingSlots = $affiliateProcessingSlots;
    }

    /**
     * @return int
     */
    public function getAffiliateOpenSubscriptionSlots(): ?int
    {
        return $this->affiliateOpenSubscriptionSlots;
    }

    /**
     * @param int $affiliateOpenSubscriptionSlots
     */
    public function setAffiliateOpenSubscriptionSlots(int $affiliateOpenSubscriptionSlots): void
    {
        $this->affiliateOpenSubscriptionSlots = $affiliateOpenSubscriptionSlots;
    }

}