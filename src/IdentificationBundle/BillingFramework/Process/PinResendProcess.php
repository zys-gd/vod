<?php

namespace IdentificationBundle\BillingFramework\Process;

use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;

/**
 * Class PinResendProcess
 */
class PinResendProcess
{
    const PROCESS_METHOD_PIN_RESEND = "pinresend";

    /**
     * @var RequestSender
     */
    private $requestSender;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PinRequestProcess constructor
     *
     * @param RequestSender $requestSender
     * @param LoggerInterface $logger
     */
    public function __construct(RequestSender $requestSender, LoggerInterface $logger)
    {
        $this->requestSender = $requestSender;
        $this->logger        = $logger;
    }

    /**
     * @param ProcessRequestParameters $parameters
     *
     * @return ProcessResult
     *
     * @throws BillingFrameworkException
     */
    public function doPinRequest(ProcessRequestParameters $parameters): ProcessResult
    {
        try {
            return $this->requestSender->sendProcessRequest(self::PROCESS_METHOD_PIN_RESEND, $parameters);
        } catch (BillingFrameworkProcessException $exception) {
            $this->logger->error('Error while trying to ident', ['params' => $parameters]);
            $code = $exception->getBillingCode() ?? $exception->getCode();
            throw new PinRequestProcessException('Error while trying to `pinResend`', $code, $exception);
        }
    }
}