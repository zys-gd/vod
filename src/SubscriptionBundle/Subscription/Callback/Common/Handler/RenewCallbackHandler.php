<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 9:02 PM
 */

namespace SubscriptionBundle\Subscription\Callback\Common\Handler;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\RenewProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Renew\OnRenewUpdater;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RenewCallbackHandler implements CallbackHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var OnRenewUpdater
     */
    private $onRenewUpdater;

    /**
     * RenewCallbackHandler constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param OnRenewUpdater           $onRenewUpdater
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, OnRenewUpdater $onRenewUpdater)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->onRenewUpdater  = $onRenewUpdater;
    }


    public function doProcess(Subscription $subscription, ProcessResult $response): void
    {
        $subscription->setCurrentStage(Subscription::ACTION_RENEW);

        $this->onRenewUpdater->updateSubscriptionByCallbackResponse($subscription, $response);

    }

    public function isSupport($type): bool
    {
        return $type === RenewProcess::PROCESS_METHOD_RENEW;
    }

    public function getPiwikEventName(): string
    {
        return 'renew';
    }

    public function afterProcess(Subscription $subscription, ProcessResult $response): void
    {
    }


    public function isActionAllowed(Subscription $subscription): bool
    {
        return true;
    }

}