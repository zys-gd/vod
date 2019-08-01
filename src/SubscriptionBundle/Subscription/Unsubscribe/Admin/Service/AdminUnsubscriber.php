<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 12:40
 */

namespace SubscriptionBundle\Subscription\Unsubscribe\Admin\Service;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Blacklist\BlacklistFactory;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Subscription\Unsubscribe\Unsubscriber;

class AdminUnsubscriber
{
    /**
     * @var BlacklistFactory
     */
    private $blacklistFactory;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var UnsubscriptionHandlerProvider
     */
    private $unsubscriptionHandlerProvider;
    /**
     * @var Unsubscriber
     */
    private $unsubscriber;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;


    /**
     * AdminUnsubscriber constructor.
     * @param BlacklistFactory              $blacklistFactory
     * @param SubscriptionRepository        $subscriptionRepository
     * @param CarrierRepositoryInterface    $carrierRepository
     * @param UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider
     * @param Unsubscriber                  $unsubscriber
     * @param EntitySaveHelper              $entitySaveHelper
     */
    public function __construct(
        BlacklistFactory $blacklistFactory,
        SubscriptionRepository $subscriptionRepository,
        CarrierRepositoryInterface $carrierRepository,
        UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider,
        Unsubscriber $unsubscriber,
        EntitySaveHelper $entitySaveHelper
    )
    {
        $this->blacklistFactory              = $blacklistFactory;
        $this->subscriptionRepository        = $subscriptionRepository;
        $this->carrierRepository             = $carrierRepository;
        $this->unsubscriptionHandlerProvider = $unsubscriptionHandlerProvider;
        $this->unsubscriber                  = $unsubscriber;
        $this->entitySaveHelper              = $entitySaveHelper;
    }

    public function unsubscribe(Subscription $subscription, bool $addToBlacklist): bool
    {
        $subscriptionPack = $subscription->getSubscriptionPack();

        try {
            $response              = $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);
            $unsubscriptionHandler = $this->unsubscriptionHandlerProvider->getUnsubscriptionHandler($subscriptionPack->getCarrier());
            $unsubscriptionHandler->applyPostUnsubscribeChanges($subscription);

            if ($unsubscriptionHandler->isPiwikNeedToBeTracked($response)) {
                $this->unsubscriber->trackEventsForUnsubscribe($subscription, $response);
            }

            if ($addToBlacklist) {
                $blackList = $this->blacklistFactory->create($subscriptionPack->getCarrier(), $subscription->getUser()->getIdentifier());
                $this->entitySaveHelper->persistAndSave($blackList);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}