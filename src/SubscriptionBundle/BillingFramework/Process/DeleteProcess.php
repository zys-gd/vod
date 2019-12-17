<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.12.19
 * Time: 17:45
 */

namespace SubscriptionBundle\BillingFramework\Process;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\Client;
use SubscriptionBundle\BillingFramework\Process\API\LinkCreator;

class DeleteProcess
{
    const PROCESS_METHOD_DELETE = "delete";

    private $requestSender;
    private $logger;
    /**
     * @var LinkCreator
     */
    private $linkCreator;

    /**
     * @param Client          $requestSender
     * @param LoggerInterface $logger
     * @param LinkCreator     $linkCreator
     */
    public function __construct(
        Client $requestSender,
        LoggerInterface $logger,
        LinkCreator $linkCreator
    )
    {
        $this->requestSender = $requestSender;
        $this->logger        = $logger;
        $this->linkCreator   = $linkCreator;
    }

    public function doDelete(string $msisdn)
    {
        return $this->requestSender->makePostRequest(
            $this->linkCreator->createProcessLink(self::PROCESS_METHOD_DELETE),
            ['userOrProviderIdent' => $msisdn]
        );

    }
}