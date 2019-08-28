<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.07.19
 * Time: 18:02
 */

namespace Carriers\TelenorPK\SMS;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Notification\SMSText\CarrierSMSHandlerInterface;

class ENTextsHandler implements CarrierSMSHandlerInterface
{


    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::TELENOR_PAKISTAN_DOT && $language->getCode() == 'en';
    }

    public function getTexts(): array
    {
        return [
            "subscribe"                     => "Successful subscription for _price_ _currency_/wk. Auto renewal on _renew_date_. To access, _shortautologin_url_. To cancel, _unsub_url_",
            "notify_renew"                  => "Subscription reminder for _currency_ _price_ /wk. Auto renewal on _renew_date_. To access, _shortautologin_url_. To cancel, _unsub_url_",
            "unsubscribe"                   => "Your subscription to 100% sport has been terminated. Thank you for your patronage. To subscribe again, _unsub_url_",
            "renewal"                       => "100% sport. Successful subscription renewal for _price_ _currency_/wk. Auto renewal on _renew_date_. To access, _shortautologin_url_. To cancel, _unsub_url_.",
            "renewal_failure"               => "We failed to renew your 100% sport subscription. We will attempt renewal over the next 6 days. To stop go to _unsub_url_",
            "renewal_failure_sub_terminate" => "You have insufficient funds for subscription renewal. Your subscription to 100% sport has been terminated. Thank you for your patronage. To subscribe again, _unsub_url_"
        ];

    }
}