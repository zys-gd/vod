<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 20.08.19
 * Time: 14:24
 */

namespace Carriers\HutchID\SMS;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Notification\SMSText\CarrierSMSHandlerInterface;

class IDTextsHandler implements CarrierSMSHandlerInterface
{

    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::HUTCH_INDONESIA && $language->getCode() == 'id';
    }

    public function getTexts(): array
    {
        return [
            'subscribe'    => "Terima kasih telah berlangganan layanan 100% Sport dengan tarif _currency_ _intprice_/3 hari selama 60 hari. Stop: UNREG 100SPORT. CS: 0895355290340",
            'unsubscribe'  => 'Terima kasih, Anda telah berhenti berlangganan layanan 100% Sports. Untuk kembali berlangganan ketik REG 100SPORT ke 98686. CS: 0895355290340',
            'notify_renew' => 'Nikmati video dan berita 100% Sport dari DOT! Download kontennya di _autologin_url_, hanya dengan _currency__intprice_/3 hr. Stop:UNREG 100SPORT. CS: 0895355290340'
        ];
    }
}