<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 16:55
 */

namespace IdentificationBundle\BillingFramework\Process;


use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\BillingFramework\Process\Exception\PinVerifyProcessException;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;

class PinVerifyProcess
{
    const PROCESS_METHOD_PIN_VERIFY = "pinverification";
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

    public function doPinVerify(ProcessRequestParameters $parameters): PinVerifyResult
    {
        try {

            $rawResponse = $this->requestSender->sendRequestWithoutResponseMapping(self::PROCESS_METHOD_PIN_VERIFY, $parameters);

            $data   = (array)$rawResponse->data;
            $result = new PinVerifyResult($data);

            return $result;

        } catch (BillingFrameworkProcessException $exception) {
            $this->logger->error('Error while trying to `pinVerify`', ['params' => $parameters]);
            throw new PinVerifyProcessException('Error while trying to `pinVerify`', $exception->getBillingCode(), $exception->getResponse()->getMessage());
        }
    }
}