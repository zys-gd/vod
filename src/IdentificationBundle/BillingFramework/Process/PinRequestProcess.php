<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 16:55
 */

namespace IdentificationBundle\BillingFramework\Process;


use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;

class PinRequestProcess
{
    const PROCESS_METHOD_PIN_REQUEST = "pinrequest";
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

    public function doPinRequest(ProcessRequestParameters $parameters): PinRequestResult
    {
        try {
            $rawResponse = $this->requestSender->sendRequestWithoutResponseMapping(self::PROCESS_METHOD_PIN_REQUEST, $parameters);

            $data              = (array)$rawResponse->data;
            $needVerifyRequest = $data['need_verify_request'] ?? false;
            $userIdentifier    = $data['user_identifier'] ?? false;

            $result = new PinRequestResult($userIdentifier, (bool)$needVerifyRequest, $data);
            return $result;

        } catch (BillingFrameworkProcessException $exception) {
            $this->logger->error('Error while trying to ident', ['params' => $parameters]);
            throw new PinRequestProcessException('Error while trying to `pinRequest`', $exception->getBillingCode() ?? 0, $exception->getResponse()->getMessage());
        }
    }
}