<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:17
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Notification;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Service\Notification\Impl\NotificationHandlerInterface;

class TelenorPKNotificationHandler implements NotificationHandlerInterface
{

    public function canHandle(Carrier $carrier): bool
    {
        return $carrier->getIdCarrier() === \AppBundle\Constant\Carrier::TELENOR_PAKISTAN;
    }

    public function isNotificationShouldBeSent(): bool
    {
        return true;
    }

    public function isProcessIdUsedInNotification(): bool
    {
        return true;
    }
}