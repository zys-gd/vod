<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 19:30
 */

namespace IdentificationBundle\BillingFramework\Process;


use IdentificationBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use IdentificationBundle\BillingFramework\Process\API\DTO\ProcessResult;
use IdentificationBundle\BillingFramework\Process\API\RequestSender;
use IdentificationBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use IdentificationBundle\BillingFramework\Process\Exception\IdentProcessException;
use Psr\Log\LoggerInterface;

class IdentProcess
{
    const PROCESS_METHOD_IDENT = "ident";
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

    public function doIdent(ProcessRequestParameters $parameters): ProcessResult
    {
        try {
            return $this->requestSender->sendProcessRequest(self::PROCESS_METHOD_IDENT, $parameters);
        } catch (BillingFrameworkException $exception) {
            $this->logger->error('Error while trying to ident', ['params' => $parameters]);
            throw new IdentProcessException('Error while trying to ident', 0, $exception);
        }
    }

}