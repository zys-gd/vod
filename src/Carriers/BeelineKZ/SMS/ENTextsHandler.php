<?php

namespace Carriers\BeelineKZ\SMS;

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
     * @param CarrierInterface $carrier
     * @param LanguageInterface $language
     *
     * @return bool
     */
    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::BEELINE_KAZAKHSTAN_DOT && $language->getCode() == 'en';
    }

    /**
     * @return array
     */
    public function getTexts(): array
    {
        return [
            'subscribe' => 'Welcome to 100% sport! Click here to watch all videos _shortautologin_url_ _price_ _currency_/day. To unsubscribe click here _unsub_url_',
            'unsubscribe' => 'You are unsubscribed from 100% Sport. To subscribe again please contact us _contact_us_url_'
        ];
    }
}