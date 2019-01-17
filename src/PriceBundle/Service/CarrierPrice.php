<?php

namespace PriceBundle\Service;

use App\Domain\Entity\Carrier;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use IdentificationBundle\Entity\User;
/**
 * A service for handling carriers
 * @package PriceBundle\Application
 */
class CarrierPrice
{
    protected $_subscriptionRepository = null;


    /**
     * @var null
     */
    protected $_subPackRepository = null;

    /**
     * CarrierPrice constructor.
     * @param SubscriptionRepository     $oSubscriptionRepository
     * @param SubscriptionPackRepository $oSubPackRepository
     */
    public function __construct(SubscriptionRepository $oSubscriptionRepository, SubscriptionPackRepository $oSubPackRepository)
    {
        $this->_subscriptionRepository = $oSubscriptionRepository;
        $this->_subPackRepository      = $oSubPackRepository;
    }

    /**
     * @param Carrier $oCarrier
     * @param null    $user
     *
     * @return null|object
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSubPackByCarrier(Carrier $oCarrier, User $user = null)
    {
        $billing_carrier_id = $oCarrier->getBillingCarrierId();
        $criteria           = [
            'carrierId' => $billing_carrier_id,
            'status'    => 1
        ];

        // get user's subPack

        if(!$oSubPack = $this->_subPackRepository->findOneBy($criteria)){
            throw new \Exception(sprintf('You have no active subscription pack for carrier `%s`', $billing_carrier_id));
        }

        if ($user instanceof User) {
            $oSubscription = $this->_subscriptionRepository->findCurrentSubscriptionByOwner($user);
            // is it still needed?
            // if ($oSubscription) {
            //     $oSubPack = $oSubscription->getPlan();
            //     $oSubPack->setCredits($oSubscription->getCredits());
            // }
        }

        return $oSubPack;
    }
}
