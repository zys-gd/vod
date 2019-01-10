<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:25
 */

namespace SubscriptionBundle\BillingFramework\Process;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;

class SubscribeProcess
{
    const PROCESS_METHOD_SUBSCRIBE = "subscribe";


    /**
     * @var RequestSender
     */
    private $requestSender;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Subscriber constructor.
     * @param RequestSender   $requestSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestSender $requestSender,
        LoggerInterface $logger
    )
    {
        $this->requestSender = $requestSender;
        $this->logger        = $logger;
    }


    /**
     * @param ProcessRequestParameters $parameters
     * @return ProcessResult
     */
    public function doSubscribe(ProcessRequestParameters $parameters): ProcessResult
    {

        try {
            return $this->requestSender->sendProcessRequest(self::PROCESS_METHOD_SUBSCRIBE, $parameters);
        } catch (BillingFrameworkException $exception) {
            $this->logger->error('Error while trying to subscribe', ['subscriptionId' => $parameters->clientId, 'params' => $parameters]);
            throw new SubscribingProcessException('Error while trying to subscribe', 0, $exception);
        }
    }


}