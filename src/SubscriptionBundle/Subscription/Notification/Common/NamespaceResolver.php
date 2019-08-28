<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.03.19
 * Time: 17:29
 */

namespace SubscriptionBundle\Subscription\Notification\Common;


use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use SubscriptionBundle\Subscription\Notification\Impl\NotificationHandlerInterface;

class NamespaceResolver
{
    public function resolveNamespace(NotificationHandlerInterface $handler, LanguageInterface $language)
    {
        return '';
    }
}