<?php

namespace SubscriptionBundle\Service\Notification\Common\SMSTexts;

interface MessageKeyHandlerInterface
{
    public function getKey(int $carrierId, string $lang): string;
}
