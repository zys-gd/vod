<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:36
 */

namespace SubscriptionBundle\Service\Action\Unsubscribe;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Event\SubscriptionUnsubscribeEvent;
use SubscriptionBundle\Service\Action\Common\CommonSubscriptionUpdater;

class OnUnsubscribeUpdater
{
    /**
     * @var CommonSubscriptionUpdater
     */
    private $commonSubscriptionUpdater;
    private $eventDispatcher;

    /**
     * OnUnsubscribeUpdater constructor.
     * @param EventDispatcherInterface                                                      $eventDispatcher
     * @param \SubscriptionBundle\Service\Action\Common\CommonSubscriptionUpdater $commonSubscriptionUpdater
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        CommonSubscriptionUpdater $commonSubscriptionUpdater
    )
    {
        $this->eventDispatcher           = $eventDispatcher;
        $this->commonSubscriptionUpdater = $commonSubscriptionUpdater;
    }

    public function updateSubscriptionByResponse(Subscription $subscription, ProcessResult $processResponse)
    {
        $this->updateSubscriptionByCallbackResponse($subscription, $processResponse);

        if ($processResponse->isRedirectRequired()) {
            $subscription->setRedirectUrl($processResponse->getUrl());
        }
    }

    final public function updateSubscriptionByCallbackResponse(Subscription $subscription, ProcessResult $processResponse)
    {

        if ($processResponse->isSuccessful()) {
            $this->applySuccess($subscription);
        }

        $this->commonSubscriptionUpdater->updateSubscriptionByCallbackResponse($subscription, $processResponse);

        if ($processResponse->isFailed()) {
            $subscription->setError($processResponse->getError());
            $this->applyFailure($subscription, $processResponse->getError());
        }
    }

    /**
     * @param Subscription $subscription
     */
    protected function applySuccess(Subscription $subscription)
    {
        $subscription->setStatus(Subscription::IS_INACTIVE);
        $this->callSubscriptionUnsubscribeEvent($subscription);
    }

    /**
     * @param Subscription $subscription
     */
    private function callSubscriptionUnsubscribeEvent(Subscription $subscription)
    {
        $event = new SubscriptionUnsubscribeEvent($subscription);
        $this->eventDispatcher->dispatch(SubscriptionUnsubscribeEvent::EVENT_NAME, $event);
    }

    protected function applyFailure(Subscription $subscription, string $errorName)
    {

        // TODO possibly outdated
        if ($subscription->getPreviousStatus() == Subscription::IS_ACTIVE) {
            $subscription->setStatus(Subscription::IS_ACTIVE);
            $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
        } else {
            $subscription->setStatus(Subscription::IS_ERROR);
        }
    }
}