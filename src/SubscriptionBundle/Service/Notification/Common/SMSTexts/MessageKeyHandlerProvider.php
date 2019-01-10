<?php

namespace SubscriptionBundle\Service\Notification\Common\SMSTexts;

class MessageKeyHandlerProvider
{
    const TYPE_SUBSCRIBE_3G = 1;
    const TYPE_SUBSCRIBE_WIFI = 2;
    const TYPE_ALERT_RENEW = 3;

    /**
     * @param int $type
     * @return MessageKeyHandlerInterface
     */
    public static function getService(int $type): MessageKeyHandlerInterface
    {
        switch ($type) {
            case self::TYPE_SUBSCRIBE_WIFI:
                $messageHandleService = new WiFiMessage();
                break;
            case self::TYPE_ALERT_RENEW:
                $messageHandleService = new RenewAlertMessage();
                break;
            case self::TYPE_SUBSCRIBE_3G:
            default:
                $messageHandleService = new DefaultMessage();
        }

        return $messageHandleService;
    }
}
