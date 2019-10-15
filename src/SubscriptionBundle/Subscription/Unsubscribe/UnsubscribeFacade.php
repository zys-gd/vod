<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.10.19
 * Time: 16:38
 */

namespace SubscriptionBundle\Subscription\Unsubscribe;


use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerProvider;

class UnsubscribeFacade
{
    /**
     * @var Unsubscriber
     */
    private $unsubscriber;
    /**
     * @var UnsubscriptionHandlerProvider
     */
    private $unsubscriptionHandlerProvider;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;


    /**
     * UnsubscribeFacade constructor.
     * @param Unsubscriber                  $unsubscriber
     * @param UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider
     * @param ApiConnector                  $crossSubscriptionApi
     */
    public function __construct(Unsubscriber $unsubscriber, UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider, ApiConnector $crossSubscriptionApi)
    {
        $this->unsubscriber                  = $unsubscriber;
        $this->unsubscriptionHandlerProvider = $unsubscriptionHandlerProvider;
        $this->crossSubscriptionApi          = $crossSubscriptionApi;
    }

    public function doFullUnsubscribe(Subscription $subscription): ProcessResult
    {
        $subscriptionPack = $subscription->getSubscriptionPack();
        $result           = $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);

        $user = $subscription->getUser();

        if ($result->isSuccessful() && $result->isFinal()) {
            $this->unsubscriber->trackEventsForUnsubscribe($subscription, $result);
            $this->crossSubscriptionApi->deregisterSubscription($user->getIdentifier(), $user->getBillingCarrierId());
        }

        return $result;
    }

    public function doUnsubscribeWithoutDeregisterFromCrossSub(Subscription $subscription): ProcessResult
    {

        $subscriptionPack = $subscription->getSubscriptionPack();
        $result           = $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);

        if ($result->isSuccessful() && $result->isFinal()) {
            $this->unsubscriber->trackEventsForUnsubscribe($subscription, $result);
        }

        return $result;
    }
}