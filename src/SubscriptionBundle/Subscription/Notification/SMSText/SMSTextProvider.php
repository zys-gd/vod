<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.10.18
 * Time: 15:41
 */

namespace SubscriptionBundle\Subscription\Notification\SMSText;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Entity\SubscriptionPack;

class SMSTextProvider
{

    /**
     * @var CarrierSMSHandlerInterface[]
     */
    private $handlers;

    public function addSMSTextHandler(CarrierSMSHandlerInterface $smsHandler)
    {
        $this->handlers[] = $smsHandler;
    }


    public function getSMSText(
        string $notificationType,
        CarrierInterface $carrier,
        SubscriptionPack $subscriptionPack,
        LanguageInterface $language
    ): string
    {
        $str = $textFromSubscriptionPack = $this->extractFromSubscriptionPack($notificationType, $subscriptionPack);
        if (trim($str)) {
            return $textFromSubscriptionPack;
        }

        $texts = [];
        foreach ($this->handlers as $handler) {
            if ($handler->isSupports($carrier, $language)) {
                $texts = $handler->getTexts();
                break;
            }
        }

        if (!$texts) {
            throw new MissingSMSTextException(
                sprintf(
                    'SMS Texts for carrier `%s` and language `%s` are not found',
                    $carrier->getBillingCarrierId(),
                    $language->getCode()
                )
            );
        }


        if (!isset($texts[$notificationType])) {
            throw new MissingSMSTextException(
                sprintf('Text for `%s` notification type is missing', $notificationType)
            );
        }

        return $texts[$notificationType];
    }

    private function extractFromSubscriptionPack($notificationType, SubscriptionPack $subscriptionPack): string
    {
        if ($notificationType === 'subscribe') {
            return (string)$subscriptionPack->getWelcomeSMSText();
        }

        if ($notificationType === 'unsubscribe') {
            return (string)$subscriptionPack->getUnsubscribeSMSText();
        }

        if ($notificationType === 'renewing') {
            return (string)$subscriptionPack->getRenewalSMSText();
        }

        return '';
    }

}