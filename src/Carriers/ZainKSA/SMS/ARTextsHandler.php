<?php

namespace Carriers\ZainKSA\SMS;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Notification\SMSText\CarrierSMSHandlerInterface;

/**
 * Class ARTextsHandler
 */
class ARTextsHandler implements CarrierSMSHandlerInterface
{
    /**
     * @param CarrierInterface  $carrier
     * @param LanguageInterface $language
     *
     * @return bool
     */
    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::ZAIN_SAUDI_ARABIA && $language->getCode() === 'ar';
    }

    /**
     * @return array
     */
    public function getTexts(): array
    {
        return[
            'subscribe'   => "لقد اشتركت في خدمة 100Sport! لمشاهدة جميع الفيديواضغط هنا _shortautologin_url_",
            'unsubscribe' => "لقد تم الغاء خدمة 100Sport بنجاح. لاعادة الاشتراك الرجاء التواصل معنا"
        ];
    }
}