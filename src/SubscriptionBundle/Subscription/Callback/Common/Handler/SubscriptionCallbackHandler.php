<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 9:02 PM
 */

namespace SubscriptionBundle\Subscription\Callback\Common\Handler;


use PiwikBundle\Service\EventPublisher;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitCompleter;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\OnSubscribeUpdater;

class SubscriptionCallbackHandler implements CallbackHandlerInterface
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

    public function doProcess(Subscription $subscription, ProcessResult $response): void
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
        return 'subscribe';
    }


    public function afterProcess(Subscription $subscription, ProcessResult $response): void
    {
        $this->completer->finishProcess($response, $subscription);

        $user = $subscription->getUser();

        $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
    }

    public function isActionAllowed(Subscription $subscription): bool
    {
        return true;
    }
}