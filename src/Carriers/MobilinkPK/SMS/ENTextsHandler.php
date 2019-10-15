<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.07.19
 * Time: 17:59
 */

namespace Carriers\MobilinkPK\SMS;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Notification\SMSText\CarrierSMSHandlerInterface;

class ENTextsHandler implements CarrierSMSHandlerInterface
{

    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::MOBILINK_PAKISTAN && $language->getCode() == 'en';
    }

    public function getTexts(): array
    {
        return [
            'subscribe'   => "Welcome to 100%sport! Click here to access _shortautologin_url_ _currency_ _price_/day. To unsub or avoid auto-renewal charges send STOP to 6170",
            'unsubscribe' => "You have been unsubscribed from 100%sport. To subscribe again, please _unsub_url_."
        ];

    }
}