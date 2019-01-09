<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:25
 */

namespace SubscriptionBundle\Service\Notification\Impl;


use AppBundle\Entity\Carrier;

class DefaultHandler implements NotificationHandlerInterface
{

    public function canHandle(Carrier $carrier): bool
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