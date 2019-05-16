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
use SubscriptionBundle\Service\CAPTool\SubscriptionLimitCompleter;

class SubscriptionCallbackHandler extends AbstractCallbackHandler
{


    /**
     * @var \SubscriptionBundle\Service\Action\Common\\SubscriptionBundle\Service\Action\Subscribe\OnSubscribeUpdater
     */
    private $onSubscribeUpdater;
    /**
     * @var SubscriptionLimitCompleter
     */
    private $completer;


    /**
     * SubscriptionCallbackHandler constructor.
     * @param \SubscriptionBundle\Service\Action\Subscribe\OnSubscribeUpdater $onSubscribeUpdater
     * @param SubscriptionLimitCompleter                                      $completer
     */
    public function __construct(
        OnSubscribeUpdater $onSubscribeUpdater,
        SubscriptionLimitCompleter $completer
    )
    {
        $this->onSubscribeUpdater = $onSubscribeUpdater;
        $this->completer          = $completer;
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


    public function afterProcess(Subscription $subscription, ProcessResult $response): void
    {
        $this->completer->finishProcess($response, $subscription);
    }
}