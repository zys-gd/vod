<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 9:02 PM
 */

namespace SubscriptionBundle\Subscription\Callback\Common\Type;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\RenewProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Renew\OnRenewUpdater;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RenewCallbackHandler extends AbstractCallbackHandler
{
    private $onRenewUpdater;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * RenewCallbackHandler constructor.
     * @param OnRenewUpdater           $onRenewUpdater
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(OnRenewUpdater $onRenewUpdater, EventDispatcherInterface $eventDispatcher)
    {
        $this->onRenewUpdater  = $onRenewUpdater;
        $this->eventDispatcher = $eventDispatcher;
    }


    public function updateSubscriptionByCallbackData(Subscription $subscription, ProcessResult $response)
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


    public function isActionAllowedForSubscription(Subscription $subscription): bool
    {
        return true;
    }

}