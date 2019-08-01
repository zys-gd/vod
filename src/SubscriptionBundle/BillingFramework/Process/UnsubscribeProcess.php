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
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;
use SubscriptionBundle\BillingFramework\Process\Exception\UnsubscribingProcessException;

class UnsubscribeProcess
{

    const PROCESS_METHOD_UNSUBSCRIBE = "unsubscribe";

    /**
     * @var RequestSender
     */
    private $requestSender;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @param LoggerInterface $logger
     * @param RequestSender   $requestSender
     */
    public function __construct(
        LoggerInterface $logger,
        RequestSender $requestSender
    )
    {
        $this->requestSender = $requestSender;
        $this->logger        = $logger;
    }

    /**
     * @param ProcessRequestParameters $parameters
     * @return ProcessResult
     * @throws UnsubscribingProcessException
     */
    public function doUnsubscribe(ProcessRequestParameters $parameters): ProcessResult
    {
        try {
            return $this->requestSender->sendProcessRequest(self::PROCESS_METHOD_UNSUBSCRIBE, $parameters);
        } catch (BillingFrameworkProcessException $exception) {
            $this->logger->error('Error while trying to unsubscribe', ['subscriptionId' => $parameters->clientId, 'params' => $parameters]);
            throw new UnsubscribingProcessException('Error while trying to unsubscribe', $exception->getBillingCode(), $exception);

        } catch (BillingFrameworkException $exception) {
            $this->logger->error('Error while trying to unsubscribe', ['subscriptionId' => $parameters->clientId, 'params' => $parameters]);
            throw new UnsubscribingProcessException('Error while trying to unsubscribe', 0, $exception);
        }

    }

}