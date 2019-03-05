<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.03.19
 * Time: 17:29
 */

namespace SubscriptionBundle\Service\Notification\Common;


use IdentificationBundle\Entity\LanguageInterface;
use SubscriptionBundle\Service\Notification\Impl\NotificationHandlerInterface;

class NamespaceResolver
{
    public function resolveNamespace(NotificationHandlerInterface $handler, LanguageInterface $language)
    {
        return '';
    }
}