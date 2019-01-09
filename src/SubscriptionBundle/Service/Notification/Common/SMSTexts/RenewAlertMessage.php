<?php

namespace SubscriptionBundle\Service\Notification\Common\SMSTexts;

use AppBundle\Constant\Carrier;

class RenewAlertMessage implements MessageKeyHandlerInterface
{
    public function getKey(int $carrierId, string $lang): string
    {
        switch ($carrierId) {
            case Carrier::TELENOR_PAKISTAN:
                return 'fortumo.telenorpk.renew.alert.en';
            case Carrier::ZONG_PAKISTAN:
                return 'dot.mt.zong.parameters';
            default:
                return 'fortumo.telenorpk.renew.alert.en';
        }
    }
}
