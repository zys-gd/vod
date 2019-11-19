<?php


namespace Carriers\BeelineKZ\SMS;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Notification\SMSText\CarrierSMSHandlerInterface;

/**
 * Class KZTextsHandler
 */
class KZTextsHandler implements CarrierSMSHandlerInterface
{
    /**
     * @param CarrierInterface $carrier
     * @param LanguageInterface $language
     *
     * @return bool
     */
    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool
    {
        return $carrier->getBillingCarrierId() === ID::BEELINE_KAZAKHSTAN_DOT && $language->getCode() == 'kz';
    }

    public function getTexts(): array
    {
        return [
            'subscribe' => '100% sports қызметіне қош келдіңіз. _shortautologin_url_ қызметіндегі барлық видеоларды күн сайын _price_ теңге төлеп көру үшін мынаны басыңыз. Жазылысты тоқтату үшін мынаны басыңыз _unsub_url_',
            'unsubscribe' => '100% sports қызметіне жазылысыңыз тоқтатылды. Жаңадан жазылу үшін бізбен байланысыңыз _contact_us_url_'
        ];
    }
}