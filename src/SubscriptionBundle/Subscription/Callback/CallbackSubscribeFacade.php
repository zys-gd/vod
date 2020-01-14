<?php


namespace SubscriptionBundle\Subscription\Callback;


use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Common\Handler\SubscriptionCallbackHandler;
use SubscriptionBundle\Subscription\Callback\Common\SubscriptionPreparer;
use SubscriptionBundle\Subscription\Subscribe\Common\SubscriptionEventTracker;

class CallbackSubscribeFacade
{
    /**
     * @var SubscriptionCallbackHandler
     */
    private $subscriptionCallbackHandler;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var SubscriptionPreparer
     */
    private $subscriptionPreparer;
    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;

    /**
     * SubscribeFacade constructor.
     *
     * @param SubscriptionCallbackHandler $subscriptionCallbackHandler
     * @param EntitySaveHelper            $entitySaveHelper
     * @param SubscriptionPreparer        $subscriptionPreparer
     * @param SubscriptionEventTracker    $subscriptionEventTracker
     * @param ApiConnector                $apiConnector
     */
    public function __construct(
        SubscriptionCallbackHandler $subscriptionCallbackHandler,
        EntitySaveHelper $entitySaveHelper,
        SubscriptionPreparer $subscriptionPreparer,
        SubscriptionEventTracker $subscriptionEventTracker,
        ApiConnector $apiConnector
    )
    {
        $this->subscriptionCallbackHandler = $subscriptionCallbackHandler;
        $this->entitySaveHelper            = $entitySaveHelper;
        $this->subscriptionPreparer        = $subscriptionPreparer;
        $this->subscriptionEventTracker    = $subscriptionEventTracker;
        $this->crossSubscriptionApi        = $apiConnector;
    }

    /**
     * @param ProcessResult $processResponse
     *
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     */
    public function doFullCallbackSubscribe(ProcessResult $processResponse)
    {
        [$carrier, $user, $subscription] = $this->subscriptionPreparer->makeUserWithSubscription($processResponse);
        $this->subscriptionCallbackHandler->doProcess($subscription, $processResponse);
        $this->entitySaveHelper->persistAndSave($subscription);
        $this->subscriptionEventTracker->trackSubscribe($subscription, $processResponse);
        $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
    }
}