<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:26
 */

namespace SubscriptionBundle\BillingFramework\Process;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;
use SubscriptionBundle\BillingFramework\Process\Exception\RenewingProcessException;

class RenewProcess
{
    const PROCESS_METHOD_RENEW = "renew";

    /**
     * @var RequestSender
     */
    private $requestSender;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Renewer constructor.
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
     * @throws RenewingProcessException
     */
    public function doRenew(ProcessRequestParameters $parameters): ProcessResult
    {
        try {
            return $this->requestSender->sendProcessRequest(self::PROCESS_METHOD_RENEW, $parameters);

        } catch (BillingFrameworkProcessException $exception) {
            $this->logger->error('Error while trying to renew', ['subscriptionId' => $parameters->clientId, 'params' => $parameters]);
            throw new RenewingProcessException('Error while trying to renew', $exception->getBillingCode(), $exception->getResponse()->getMessage());

        } catch (BillingFrameworkException $exception) {
            $this->logger->error('Error while trying to renew', ['subscriptionId' => $parameters->clientId, 'params' => $parameters]);
            throw new RenewingProcessException('Error while trying to renew', 0, $exception);
        }

    }


}