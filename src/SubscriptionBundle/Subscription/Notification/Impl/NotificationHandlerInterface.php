<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:18
 */

namespace SubscriptionBundle\Subscription\Notification\Impl;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;

interface NotificationHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool;

    public function isNotificationShouldBeSent(): bool;

    public function isProcessIdUsedInNotification(): bool;

    public function getSmsLanguage(): LanguageInterface;

    public function getMessageNamespace(): ?string;

}