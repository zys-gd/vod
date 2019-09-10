<?php

namespace Carriers\ZainKSA\SMS;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Notification\SMSText\CarrierSMSHandlerInterface;

/**
 * Class ENTextsHandler
 */
class ENTextsHandler implements CarrierSMSHandlerInterface
{
    /**
     * @param CarrierInterface  $carrier
     * @param LanguageInterface $language
     *
     * @return bool
     */
    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::ZAIN_SAUDI_ARABIA && $language->getCode() == 'en';
    }

    /**
     * @return array
     */
    public function getTexts(): array
    {
        return [
            'subscribe'   => "Welcome to 100%sport! Click here to watch all videos _shortautologin_url_ _currency_ _price_/day. To unsub visit _unsub_url_.",
            'unsubscribe' => "You have been unsubscribed from 100%sport. To subscribe again, please _contact_us_url_."
        ];
    }
}