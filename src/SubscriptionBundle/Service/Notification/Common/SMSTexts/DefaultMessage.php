<?php

namespace SubscriptionBundle\Service\Notification\Common\SMSTexts;

use AppBundle\Constant\Carrier;

class DefaultMessage implements MessageKeyHandlerInterface
{
    public function getKey(int $carrierId, string $lang): string
    {
        switch ($carrierId) {
            case Carrier::SMARTFEN_INDONESIA:
                return 'dot.mt.smart.parameters.id';
            case Carrier::ZONG_PAKISTAN:
                return 'dot.mt.zong.parameters';
            case Carrier::MTN_SUDAN:
                return 'dot.mt.mtnsd.parameters';
            case Carrier::HUTCH3_INDONESIA:
                return 'dot.mt.hutch3.parameters';
            case Carrier::INDOSAT_INDONESIA:
                return 'dot.mt.indosat.parameters';
            case Carrier::OOREDOO_QATAR:
                return 'dot.mt.ooredoo.qa.parameters';
            case Carrier::TELENOR_PAKISTAN:
                return 'fortumo.telenorpk.parameters.en';
            case Carrier::CELLCARD_CAMBODIA:
                return 'fortumo.cellcardkh.parameters.en';
            case Carrier::OOREDOO_OMAN:
                return 'dot.mt.ooredoo.om.parameters';
            default:
                return 'dot.mt.parameters';
        }
    }
}
