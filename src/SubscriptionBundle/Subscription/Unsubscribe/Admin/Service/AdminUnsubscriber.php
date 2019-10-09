<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 12:40
 */

namespace SubscriptionBundle\Subscription\Unsubscribe\Admin\Service;


use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Blacklist\BlacklistFactory;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeFacade;
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
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var UnsubscribeFacade
     */
    private $unsubscribeFacade;


    /**
     * AdminUnsubscriber constructor.
     * @param BlacklistFactory           $blacklistFactory
     * @param SubscriptionRepository     $subscriptionRepository
     * @param CarrierRepositoryInterface $carrierRepository
     * @param EntitySaveHelper           $entitySaveHelper
     * @param UnsubscribeFacade          $unsubscribeFacade
     */
    public function __construct(
        BlacklistFactory $blacklistFactory,
        SubscriptionRepository $subscriptionRepository,
        CarrierRepositoryInterface $carrierRepository,
        EntitySaveHelper $entitySaveHelper,
        UnsubscribeFacade $unsubscribeFacade
    )
    {
        $this->blacklistFactory       = $blacklistFactory;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->carrierRepository      = $carrierRepository;
        $this->entitySaveHelper       = $entitySaveHelper;
        $this->unsubscribeFacade      = $unsubscribeFacade;
    }

    public function unsubscribe(Subscription $subscription, bool $addToBlacklist): bool
    {
        $subscriptionPack = $subscription->getSubscriptionPack();

        try {

            $this->unsubscribeFacade->doFullUnsubscribe($subscription);

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