<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 28/08/17
 * Time: 6:35 PM
 */

namespace SubscriptionBundle\Subscription\Callback\Common\Type;


use PiwikBundle\Service\EventPublisher;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Callback\Common\SubscriptionStatusChanger;
use SubscriptionBundle\Subscription\Unsubscribe\OnUnsubscribeUpdater;

class UnsubscriptionCallbackHandler extends AbstractCallbackHandler
{
    private $onUnsubscribeUpdater;


    /**
     * UnsubscriptionCallbackHandler constructor.
     * @param OnUnsubscribeUpdater $onUnsubscribeUpdater
     */
    public function __construct(
        OnUnsubscribeUpdater $onUnsubscribeUpdater
    )
    {
        $this->onUnsubscribeUpdater = $onUnsubscribeUpdater;
    }


    public function isSupport($type): bool
    {
        return $type == UnsubscribeProcess::PROCESS_METHOD_UNSUBSCRIBE;
    }


    public function getPiwikEventName(): string
    {
        return EventPublisher::TRACK_UNSUBSCRIBE;
    }


    public function updateSubscriptionByCallbackData(Subscription $subscription, ProcessResult $response)
    {
        $subscription->setCurrentStage(Subscription::ACTION_UNSUBSCRIBE);


        $this->onUnsubscribeUpdater->updateSubscriptionByCallbackResponse($subscription, $response);
    }

    public function afterProcess(Subscription $subscription, ProcessResult $response): void
    {
        // TODO: Implement afterProcess() method.
    }
}