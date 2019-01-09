<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 9:02 PM
 */

namespace SubscriptionBundle\Service\Callback\Common\Type;


use PiwikBundle\Service\NewTracker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\RenewProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Event\SubscriptionRenewSuccessEvent;
use SubscriptionBundle\Service\Action\Renew\OnRenewUpdater;
use SubscriptionBundle\Service\Callback\Common\SubscriptionStatusChanger;

class RenewCallbackHandler extends AbstractCallbackHandler
{
    /**
     * @var \SubscriptionBundle\BillingFramework\\SubscriptionBundle\Service\Action\Common\\SubscriptionBundle\Service\Action\Renew\OnRenewUpdater
     */
    private $onRenewUpdater;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * RenewCallbackHandler constructor.
     * @param OnRenewUpdater $onRenewUpdater
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(OnRenewUpdater $onRenewUpdater, EventDispatcherInterface $eventDispatcher)
    {
        $this->onRenewUpdater = $onRenewUpdater;
        $this->eventDispatcher = $eventDispatcher;
    }


    public function updateSubscriptionByCallbackData(Subscription $subscription, ProcessResult $response)
    {
        $subscription->setCurrentStage(Subscription::ACTION_RENEW);

        $this->onRenewUpdater->updateSubscriptionByCallbackResponse($subscription, $response);

        $response->isSuccessful() && $this->callSubscriptionRenewSuccessEvent($subscription);
    }

    public function isSupport($type): bool
    {
        return $type === RenewProcess::PROCESS_METHOD_RENEW;
    }

    public function getPiwikEventName(): string
    {
        return NewTracker::TRACK_RENEW;
    }

    /**
     * @param Subscription $subscription
     */
    private function callSubscriptionRenewSuccessEvent(Subscription $subscription)
    {
        $event = new SubscriptionRenewSuccessEvent($subscription);
        $this->eventDispatcher->dispatch(SubscriptionRenewSuccessEvent::EVENT_NAME, $event);
    }
}