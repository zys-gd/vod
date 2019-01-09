<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 13:30
 */

namespace IdentificationBundle\BillingFramework\Process;


use IdentificationBundle\BillingFramework\Process\Exception\IdentProcessException;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;

class AfterPixelProcess
{
    const PROCESS_AFTER_PIXEL = 'identafterpixel';
    private $requestSender;
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        RequestSender $requestSender
    )
    {
        $this->requestSender = $requestSender;
        $this->logger        = $logger;
    }

    public function doAfterIdent(ProcessRequestParameters $parameters): ProcessResult
    {
        try {
            return $this->requestSender->sendProcessRequest(self::PROCESS_AFTER_PIXEL, $parameters);
        } catch (BillingFrameworkException $exception) {
            $this->logger->error('Error while trying to ident', ['params' => $parameters]);
            throw new IdentProcessException('Error while trying to identAfterPixel', 0, $exception);
        }
    }
}