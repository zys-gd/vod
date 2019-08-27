<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.05.18
 * Time: 10:50
 */

namespace SubscriptionBundle\Subscription\Common;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Event\SubscriptionEvent;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FakeResponseProvider
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    /**
     * PromotionalResponseProvider constructor.
     */
    public function __construct(LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $this->logger          = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }


    public function getDummyResult(
        Subscription $subscription,
        string $type,
        string $status = ProcessResult::STATUS_SUCCESSFUL
    ): ProcessResult
    {
        $this->logger->info('Using promotional response');

        $id             = null;
        $subtype        = 'final';
        $clientId       = $subscription->getUuid();
        $url            = null;
        $error          = null;
        $chargeValue    = null;
        $chargeCurrency = null;
        $chargeProduct  = null;
        $chargeTier     = null;
        $chargeStrategy = $subscription->getSubscriptionPack()->getBuyStrategyId();

        $message = "Dummy successful subscription message";

        $billingFrameworkProcessedResponse = new ProcessResult($id,
            $subtype,
            $clientId,
            $url,
            $status,
            $error,
            $chargeValue,
            $chargeCurrency,
            $chargeProduct,
            $chargeTier,
            $chargeStrategy,
            $type,
            $message
        );

        return $billingFrameworkProcessedResponse;

    }

}