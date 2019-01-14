<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:25
 */

namespace SubscriptionBundle\Service\Notification\Impl;


use IdentificationBundle\Entity\CarrierInterface;

class DefaultHandler implements NotificationHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
    }

    public function isNotificationShouldBeSent(): bool
    {
        return true;
    }

    public function isProcessIdUsedInNotification(): bool
    {
        return false;
    }
}