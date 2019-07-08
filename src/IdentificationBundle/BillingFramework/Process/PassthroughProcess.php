<?php


namespace IdentificationBundle\BillingFramework\Process;


use IdentificationBundle\BillingFramework\Process\Exception\IdentProcessException;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;

class PassthroughProcess
{
    /**
     * @var RequestSender
     */
    private $requestSender;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PassthroughProcess constructor.
     *
     * @param LoggerInterface $logger
     * @param RequestSender   $requestSender
     */
    public function __construct(LoggerInterface $logger, RequestSender $requestSender)
    {
        $this->requestSender = $requestSender;
        $this->logger        = $logger;
    }


    public function runPassthrough(ProcessRequestParameters $parameters)
    {
        try {
            return $this->requestSender->sendProcessRequest('passthrough', $parameters);
        } catch (BillingFrameworkException $exception) {
            $this->logger->error('Error while trying to passthrough ident');
            throw new IdentProcessException('Error while trying to ident', 0, $exception);
        }
    }
}