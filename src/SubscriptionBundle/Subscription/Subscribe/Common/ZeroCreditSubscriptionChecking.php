<?php

namespace SubscriptionBundle\Subscription\Subscribe\Common;

use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;

/**
 * Class ZeroCreditSubscriptionChecking
 */
class ZeroCreditSubscriptionChecking
{
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * ZeroCreditSubscriptionChecking constructor
     *
     * @param SubscriptionPackProvider   $subscriptionPackProvider
     * @param CarrierRepositoryInterface $carrierRepository
     */
    public function __construct(
        SubscriptionPackProvider $subscriptionPackProvider,
        CarrierRepositoryInterface $carrierRepository
    ) {
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * @param int                    $billingCarrierId
     * @param CampaignInterface|null $campaign
     *
     * @return bool
     */
    public function isZeroCreditAvailable(int $billingCarrierId, CampaignInterface $campaign = null): bool
    {
        $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPackFromCarrier($carrier);

        return $subscriptionPack->isZeroCreditSubAvailable() && (!$campaign || $campaign->isZeroCreditSubAvailable());
    }
}