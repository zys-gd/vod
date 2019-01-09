<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 14:19
 */

namespace SubscriptionBundle\Service;


use AppBundle\Entity\Carrier;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Repository\SubscriptionPack\SubscriptionPackRepository;
use UserBundle\Entity\BillableUser;

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
     * @param $billableUser
     * @return SubscriptionPack
     * @throws ActiveSubscriptionPackNotFound
     */
    public function getActiveSubscriptionPack(BillableUser $billableUser): SubscriptionPack
    {
        $carrierId = $billableUser->getCarrier()->getIdCarrier();

        /** @var SubscriptionPack $subscriptionPack */
        $subscriptionPack = $this->subscriptionPackRepository->findOneBy([
            'carrierId' => $carrierId,
            'status'    => SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK
        ]);

        if (!$subscriptionPack) {
            throw new ActiveSubscriptionPackNotFound(
                "Active subscription pack not found for carrier id {$carrierId}"
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

        $carrierId = $carrier->getIdCarrier();

        /** @var SubscriptionPack $subscriptionPack */
        $subscriptionPack = $this->subscriptionPackRepository->findOneBy([
            'carrierId' => $carrierId,
            'status'    => SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK
        ]);

        return $subscriptionPack;
    }

}