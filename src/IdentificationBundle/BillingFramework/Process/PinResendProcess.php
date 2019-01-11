<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 16:55
 */

namespace IdentificationBundle\BillingFramework\Process;


use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;

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
     * PinRequestProcess constructor.
     */
    public function __construct(RequestSender $requestSender, LoggerInterface $logger)
    {
        $this->requestSender = $requestSender;
        $this->logger        = $logger;
    }

    public function doPinRequest(ProcessRequestParameters $parameters): ProcessResult
    {
        try {
            return $this->requestSender->sendProcessRequest(self::PROCESS_METHOD_PIN_RESEND, $parameters);
        } catch (BillingFrameworkException $exception) {
            $this->logger->error('Error while trying to ident', ['params' => $parameters]);
            throw new PinRequestProcessException('Error while trying to `pinResend`', 0, $exception);
        }
    }
}