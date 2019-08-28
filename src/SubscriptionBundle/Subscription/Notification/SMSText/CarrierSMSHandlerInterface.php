<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.07.19
 * Time: 17:46
 */

namespace SubscriptionBundle\Subscription\Notification\SMSText;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;

interface CarrierSMSHandlerInterface
{

    public function isSupports(CarrierInterface $carrier, LanguageInterface $language): bool;

    public function getTexts(): array;

}