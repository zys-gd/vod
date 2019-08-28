<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:23
 */

namespace SubscriptionBundle\Subscription\Notification\Impl;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;

class NotificationHandlerProvider
{
    /**
     * @var DefaultHandler
     */
    private $defaultNotificationHandler;

    /**
     * @var NotificationHandlerInterface[]
     */
    private $notificationHandlers;

    /**
     * NotificationHandlerProvider constructor.
     * @param DefaultHandler $handler
     */
    public function __construct(DefaultHandler $handler)
    {
        $this->defaultNotificationHandler = $handler;
    }

    public function get(string $processType, CarrierInterface $carrier): NotificationHandlerInterface
    {
        if (isset($this->notificationHandlers[$processType])) {
            /** @var NotificationHandlerInterface[] $handlers */
            $handlers = $this->notificationHandlers[$processType];
            foreach ($handlers as $notificationHandler) {
                if ($notificationHandler->canHandle($carrier)) {
                    return $notificationHandler;
                }
            }
        }
        return $this->defaultNotificationHandler;
    }

    /**
     * @param NotificationHandlerInterface $handler
     * @param string                       $processType
     */
    public function addHandler(NotificationHandlerInterface $handler, string $processType)
    {
        $this->notificationHandlers[$processType][] = $handler;
    }

}