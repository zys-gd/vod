<?php

namespace Carriers\ZongPK\SMS;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Notification\SMSText\CarrierSMSHandlerInterface;

class ENTextsHandler implements CarrierSMSHandlerInterface
{

    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::ZONG_PAKISTAN && $language->getCode() == 'en';
    }

    public function getTexts(): array
    {
        return [
            'subscribe'       => "Welcome to 100%sport! Click here to access _shortautologin_url_ for _currency_ _price_/day. To
unsubscribe send STOP 100 to 3557",
            'unsubscribe'     => "You have been unsubscribed from 100%sport. To subscribe again, please _contact_us_url_.",
            'notify_renew'    => '100%sport. Charge notification for 50 PKR/week. To access _shortautologin_url_',
            'renewal_failure' => 'You have been unsubscribed from 100%sport due to insufficient balance.'
        ];

    }
}