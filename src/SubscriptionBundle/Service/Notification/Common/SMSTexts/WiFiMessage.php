<?php

namespace SubscriptionBundle\Service\Notification\Common\SMSTexts;

use AppBundle\Constant\Carrier;

class WiFiMessage implements MessageKeyHandlerInterface
{
    public function getKey(int $carrierId, string $lang): string
    {
        switch ($carrierId) {
            case Carrier::TELENOR_PAKISTAN:
                return 'fortumo.telenorpk.parameters.en';
            case Carrier::CELLCARD_CAMBODIA:
                return 'fortumo.cellcardkh.parameters.en';
            case Carrier::OOREDOO_OMAN:
                return 'dot.mt.ooredoo.om.wifi.parameters';
            case 2008:
                return ($lang === 'ar') ? 'dot.mt.du.uae.ar.parameters' : 'dot.mt.du.uae.en.parameters';
        }

        switch ($lang) {
            case 'ar':
                return 'dot.mt.autologin.parameters.ar';
            case 'fr':
                return 'dot.mt.autologin.parameters.fr';
            case 'ru':
                return 'dot.mt.autologin.parameters.ru';
            case 'th':
                return 'dot.mt.autologin.parameters.th';
            case 'en':
            default:
                return 'dot.mt.autologin.parameters.en';
        }
    }
}
