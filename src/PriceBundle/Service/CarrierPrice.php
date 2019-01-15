<?php

namespace PriceBundle\Service;

use App\Domain\Entity\Carrier;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;

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
     * CarrierPrice constructor
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
     * @param null    $billableUser
     * @return null|object
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSubPackByCarrier(Carrier $oCarrier, $billableUser = null)
    {
        $billing_carrier_id = $oCarrier->getUuid();
        $criteria           = [
            'carrierUuid' => $billing_carrier_id,
            'status'    => 1
        ];

        // get user's subPack

        if(!$oSubPack = $this->_subPackRepository->findOneBy($criteria)){
            throw new \Exception(sprintf('You have no active subscription pack for carrier `%s`', $billing_carrier_id));
        }

        if ($billableUser instanceof BillableUser) {
            $oSubscription = $this->_subscriptionRepository->findCurrentSubscriptionByOwner($billableUser);
            // is it still needed?
            // if ($oSubscription) {
            //     $oSubPack = $oSubscription->getPlan();
            //     $oSubPack->setCredits($oSubscription->getCredits());
            // }
        }

        return $oSubPack;
    }
}
