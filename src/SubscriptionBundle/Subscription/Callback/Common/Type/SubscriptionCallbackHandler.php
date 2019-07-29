<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 9:02 PM
 */

namespace SubscriptionBundle\Subscription\Callback\Common\Type;


use PiwikBundle\Service\PiwikTracker;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitCompleter;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\OnSubscribeUpdater;

class SubscriptionCallbackHandler extends AbstractCallbackHandler
{


    /**
     * @var \SubscriptionBundle\Subscription\Common\\SubscriptionBundle\Subscription\Subscribe\OnSubscribeUpdater
     */
    private $onSubscribeUpdater;
    /**
     * @var SubscriptionLimitCompleter
     */
    private $completer;
    /**
     * @var \Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector
     */
    private $crossSubscriptionApi;


    /**
     * SubscriptionCallbackHandler constructor.
     * @param \SubscriptionBundle\Subscription\Subscribe\OnSubscribeUpdater $onSubscribeUpdater
     * @param SubscriptionLimitCompleter                                    $completer
     */
    public function __construct(
        OnSubscribeUpdater $onSubscribeUpdater,
        SubscriptionLimitCompleter $completer,
        ApiConnector $crossSubscriptionApi
    )
    {
        $this->onSubscribeUpdater   = $onSubscribeUpdater;
        $this->completer            = $completer;
        $this->crossSubscriptionApi = $crossSubscriptionApi;
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
        return PiwikTracker::TRACK_SUBSCRIBE;
    }


    public function afterProcess(Subscription $subscription, ProcessResult $response): void
    {
        $this->completer->finishProcess($response, $subscription);

        $user = $subscription->getUser();

        $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
    }
}