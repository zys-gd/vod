<?php

namespace SubscriptionBundle\Carriers\VivaBahrainMM\Callback;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Callback\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Callback\Common\SubscriptionPreparer;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomConversionTrackingRules;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomFlow;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 11:18
 */
class VivaBahrainMMSubscribeCallbackHandler implements CarrierCallbackHandlerInterface, HasCustomConversionTrackingRules, HasCustomFlow
{
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;
    /**
     * @var ProcessResponseMapper
     */
    private $processResponseMapper;
    /**
     * @var SubscriptionPreparer
     */
    private $subscriptionPreparer;

    /**
     * OrangeTNSubscribeCallbackHandler constructor.
     *
     * @param CommonFlowHandler     $commonFlowHandler
     * @param ProcessResponseMapper $processResponseMapper
     * @param SubscriptionPreparer  $subscriptionPreparer
     */
    public function __construct(
        CommonFlowHandler $commonFlowHandler,
        ProcessResponseMapper $processResponseMapper,
        SubscriptionPreparer $subscriptionPreparer
    )
    {
        $this->commonFlowHandler     = $commonFlowHandler;
        $this->processResponseMapper = $processResponseMapper;
        $this->subscriptionPreparer  = $subscriptionPreparer;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId == ID::VIVA_BAHRAIN_MM;
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        // TODO: Implement onRenewSendSuccess() method.
    }

    public function isConversionNeedToBeTracked(ProcessResult $result): bool
    {
        return true;
    }

    /**
     * @param Request $request
     * @param string  $type
     *
     * @throws \Exception
     */
    public function process(Request $request, string $type)
    {
        $requestParams   = (Object)$request->request->all();
        $processResponse = $this->processResponseMapper->map($type, (object)['data' => $requestParams]);

        $this->subscriptionPreparer->makeUserWithSubscription($processResponse);
        $this->commonFlowHandler->process($request, ID::VIVA_BAHRAIN_MM, $type);
    }
}