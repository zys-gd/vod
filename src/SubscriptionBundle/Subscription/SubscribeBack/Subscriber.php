<?php


namespace SubscriptionBundle\Subscription\SubscribeBack;


use IdentificationBundle\BillingFramework\Data\DataProvider;
use IdentificationBundle\Entity\User;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
use SubscriptionBundle\Subscription\Subscribe\OnSubscribeUpdater;

class Subscriber
{
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var SubscriptionFactory
     */
    private $subscriptionCreator;
    /**
     * @var OnSubscribeUpdater
     */
    private $subscribeUpdater;
    /**
     * @var DataProvider
     */
    private $dataProvider;
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;
    /**
     * @var ApiConnector
     */
    private $crossSubscription;

    public function __construct(
        EntitySaveHelper $entitySaveHelper,
        SubscriptionFactory $subscriptionCreator,
        OnSubscribeUpdater $subscribeUpdater,
        DataProvider $dataProvider,
        ProcessResultSuccessChecker $resultSuccessChecker,
        ApiConnector $crossSubscription
    )
    {
        $this->entitySaveHelper     = $entitySaveHelper;
        $this->subscriptionCreator  = $subscriptionCreator;
        $this->subscribeUpdater     = $subscribeUpdater;
        $this->dataProvider         = $dataProvider;
        $this->resultSuccessChecker = $resultSuccessChecker;
        $this->crossSubscription    = $crossSubscription;
    }

    /**
     * @param User             $user
     * @param SubscriptionPack $subscriptionPack
     * @param string           $billingProcessId
     * @param string|null      $affiliateToken
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException*@throws \Exception
     */
    public function subscribe(User $user, SubscriptionPack $subscriptionPack, string $billingProcessId, string $affiliateToken = null): array
    {
        $processResult   = $this->dataProvider->getProcessData($billingProcessId);
        $newSubscription = $this->createPendingSubscription($user, $subscriptionPack);

        $newSubscription->setAffiliateToken($affiliateToken);

        $this->subscribeUpdater->updateSubscriptionByResponse($newSubscription, $processResult);

        if ($newSubscription->isSubscribed() && $this->resultSuccessChecker->isSuccessful($processResult)) {
            $this->crossSubscription->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
        }

        $this->entitySaveHelper->persistAndSave($user);
        $this->entitySaveHelper->persistAndSave($newSubscription);

        return [$newSubscription, $processResult];
    }

    /**
     * @param User             $User
     * @param SubscriptionPack $plan
     *
     * @return Subscription
     * @throws \Exception
     */
    private function createPendingSubscription(User $User, SubscriptionPack $plan): Subscription
    {
        $subscription = $this->subscriptionCreator->create($User, $plan);
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
        $this->entitySaveHelper->persistAndSave($subscription);
        return $subscription;
    }
}