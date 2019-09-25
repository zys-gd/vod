<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.09.19
 * Time: 14:47
 */

namespace SubscriptionBundle\Subscription\Subscribe\Common;


use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;

class PendingSubscriptionCreator
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubscriptionFactory
     */
    private $factory;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    /**
     * PendingSubscriptionCreator constructor.
     * @param LoggerInterface     $logger
     * @param SubscriptionFactory $factory
     */
    public function __construct(LoggerInterface $logger, SubscriptionFactory $factory, EntitySaveHelper $entitySaveHelper)
    {
        $this->logger           = $logger;
        $this->factory          = $factory;
        $this->entitySaveHelper = $entitySaveHelper;
    }


    /**
     * @param User             $User
     * @param SubscriptionPack $plan
     *
     * @param null             $campaignData
     * @return Subscription
     */
    public function createPendingSubscription(User $User, SubscriptionPack $plan, $campaignData = null): Subscription
    {

        $this->logger->debug('Creating subscription', ['campaignData' => $campaignData]);

        $subscription = $this->factory->create($User, $plan);
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
        $subscription->setAffiliateToken(json_encode($campaignData));

        $this->entitySaveHelper->persistAndSave($subscription);

        return $subscription;
    }

}