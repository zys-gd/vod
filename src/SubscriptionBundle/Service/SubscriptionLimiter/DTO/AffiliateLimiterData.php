<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\DTO;

use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class AffiliateLimiterData
{
    /**
     * @var AffiliateInterface
     */
    private $affiliate;
    /**
     * @var ConstraintByAffiliate
     */
    private $constraintByAffiliate;
    /**
     * @var int|null
     */
    private $processingSlots;
    /**
     * @var int|null
     */
    private $openSubscriptionSlots;
    /**
     * @var int
     */
    private $billingCarrierId;

    /**
     * AffiliateLimiterData constructor.
     *
     * @param AffiliateInterface    $affiliate
     * @param ConstraintByAffiliate $constraintByAffiliate
     * @param int                   $billingCarrierId
     * @param int|null              $processingSlots
     * @param int|null              $openSubscriptionSlots
     */
    public function __construct(AffiliateInterface $affiliate,
        ConstraintByAffiliate $constraintByAffiliate,
        int $billingCarrierId,
        ?int $processingSlots,
        ?int $openSubscriptionSlots)
    {
        $this->affiliate             = $affiliate;
        $this->constraintByAffiliate = $constraintByAffiliate;
        $this->processingSlots       = $processingSlots;
        $this->openSubscriptionSlots = $openSubscriptionSlots;
        $this->billingCarrierId      = $billingCarrierId;
    }

    /**
     * @return int
     */
    public function getBillingCarrierId(): int
    {
        return $this->billingCarrierId;
    }

    /**
     * @return AffiliateInterface
     */
    public function getAffiliate(): AffiliateInterface
    {
        return $this->affiliate;
    }

    /**
     * @return ConstraintByAffiliate
     */
    public function getConstraintByAffiliate(): ConstraintByAffiliate
    {
        return $this->constraintByAffiliate;
    }

    /**
     * @return int|null
     */
    public function getProcessingSlots(): ?int
    {
        return $this->processingSlots;
    }

    /**
     * @return int|null
     */
    public function getOpenSubscriptionSlots(): ?int
    {
        return $this->openSubscriptionSlots;
    }
}