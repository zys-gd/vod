<?php

namespace SubscriptionBundle\Service;


use App\Domain\Entity\Carrier;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Repository\SubscriptionPackRepository;

class SubscriptionPackProvider
{
    /**
     * @var SubscriptionPackRepository
     */
    private $subscriptionPackRepository;

    /**
     * SubscriptionPackProvider constructor.
     * @param SubscriptionPackRepository $subscriptionPackRepository
     */
    public function __construct(SubscriptionPackRepository $subscriptionPackRepository)
    {
        $this->subscriptionPackRepository = $subscriptionPackRepository;
    }

    /**
     * @param $user
     * @return SubscriptionPack
     * @throws ActiveSubscriptionPackNotFound
     */
    public function getActiveSubscriptionPack(User $user): SubscriptionPack
    {
        $billingCarrierId = $user->getCarrier()->getBillingCarrierId();

        /** @var SubscriptionPack $subscriptionPack */
        $subscriptionPack = $this->subscriptionPackRepository->findOneBy([
            'carrierId' => $user->getCarrier()->getBillingCarrierId(),
            'status'  => SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK
        ]);

        if (!$subscriptionPack) {
            throw new ActiveSubscriptionPackNotFound(
                "Active subscription pack not found for carrier id {$billingCarrierId}"
            );
        }
        return $subscriptionPack;
    }


    /**
     * API Created for returning active subscription pack from carrier
     * @param Carrier|bool $carrier
     * @return null| SubscriptionPack
     */
    public function getActiveSubscriptionPackFromCarrier(Carrier $carrier = null)
    {
        if (!$carrier) {
            return null;
        }

        $billingCarrierId = $carrier->getBillingCarrierId();

        /** @var SubscriptionPack $subscriptionPack */
        $subscriptionPack = $this->subscriptionPackRepository->findOneBy([
            'billingCarrierId' => $billingCarrierId,
            'status'           => SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK
        ]);

        return $subscriptionPack;
    }

}