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

class URTextsHandler implements CarrierSMSHandlerInterface
{


    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::TELENOR_PAKISTAN_DOT && $language->getCode() == 'ur';
    }

    public function getTexts(): array
    {
        return [
            "subscribe"                     => "100% sport _currency_ _price_/week kharednay ka shukriya, Agla charge _renew_date_ ko hoga. Istamal karne ke liye _shortautologin_url_. Khatam k liye sms DJ STOP 100 to 5716",
            "notify_renew"                  => "100% sport _currency_ _price_/week _renew_date_ ko renew ki jayay gi. Istamal karne ke liye _shortautologin_url_. Khatam k liye sms DJ STOP 100 to 5716",
            "unsubscribe"                   => "100% sport ki subcription khatam ker di gayi hai. 100% sport phir sy hasil kernay ke liye _unsub_url_",
            "renewal"                       => "100% sport Rs _price_/week renew kar di gayi. Agla charge _renew_date_ ko hoga. Istamal karne ke liye _shortautologin_url_. Khatam k liye sms DJ STOP 100 to 5716",
            "renewal_failure"               => "100% sport ki subscription renew nahi ho saki. Agley 6 dino main renewal ki koshish karainge.Khatam k liye sms DJ STOP 100 to 5716",
            "renewal_failure_sub_terminate" => "100% sport ko renew karnay ka balance nahi hay. Apki subscription khatam kar di gaye hai. Phir se hasil karne ke liye _unsub_url_",
        ];

    }
}