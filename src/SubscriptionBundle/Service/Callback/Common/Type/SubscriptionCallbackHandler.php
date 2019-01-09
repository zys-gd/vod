<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 9:02 PM
 */

namespace SubscriptionBundle\Service\Callback\Common\Type;


use PiwikBundle\Service\NewTracker;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\OnSubscribeUpdater;

class SubscriptionCallbackHandler extends AbstractCallbackHandler
{


    /**
     * @var \SubscriptionBundle\Service\Action\Common\\SubscriptionBundle\Service\Action\Subscribe\OnSubscribeUpdater
     */
    private $onSubscribeUpdater;


    /**
     * SubscriptionCallbackHandler constructor.
     * @param \SubscriptionBundle\Service\Action\Subscribe\OnSubscribeUpdater $onSubscribeUpdater
     */
    public function __construct(
        OnSubscribeUpdater $onSubscribeUpdater
    )
    {
        $this->onSubscribeUpdater = $onSubscribeUpdater;
    }

    public function updateSubscriptionByCallbackData(Subscription $subscription, ProcessResult $response)
    {
        $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);

        $this->onSubscribeUpdater->updateSubscriptionByCallbackResponse($subscription, $response);

    }

    public function isSupport($type): bool
    {
        return $type === SubscribeProcess::PROCESS_METHOD_SUBSCRIBE;
    }

    public function getPiwikEventName(): string
    {
        return NewTracker::TRACK_SUBSCRIBE;
    }


}