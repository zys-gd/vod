<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.06.18
 * Time: 16:52
 */

namespace SubscriptionBundle\BillingFramework\Notification\API;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\BillingOptionsProvider;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\NotificationMessage;
use Symfony\Component\Routing\RouterInterface;

class MessageCreator
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var BillingOptionsProvider
     */
    private $billingOptionsProvider;

    /**
     * MessageCreator constructor.
     * @param LoggerInterface        $logger
     * @param RouterInterface        $router
     * @param BillingOptionsProvider $billingOptionsProvider
     */
    public function __construct(
        LoggerInterface $logger,
        RouterInterface $router,
        BillingOptionsProvider $billingOptionsProvider
    )
    {
        $this->logger                 = $logger;
        $this->router                 = $router;
        $this->billingOptionsProvider = $billingOptionsProvider;
    }

    public function createMessage(string $identifier, string $type, string $body, int $operatorId): NotificationMessage
    {

        $notificationMessage = new NotificationMessage();
        $notificationMessage->setClient($this->billingOptionsProvider->getClientId());
        $notificationMessage->setType($type);
        $notificationMessage->setBody($body);
        $notificationMessage->setPhone($identifier);
        $notificationMessage->setOperatorId($operatorId);


        return $notificationMessage;
    }
}