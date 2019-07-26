<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.10.18
 * Time: 15:41
 */

namespace SubscriptionBundle\Subscription\Notification\Common;


use AppBundle\Service\Domain\Carrier\CarrierProvider;
use CommonDataBundle\Entity\Interfaces\LanguageInterface;
use CommonDataBundle\Repository\Interfaces\LanguageRepositoryInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\BillingFramework\Listener\NotificationContentProvider;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Notification\Common\SMSTexts\MessageKeyHandlerProvider;

class SMSTextProvider
{
    /**
     * @var array
     */
    private $hardcodedMessages;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierProvider;
    /**
     * @var LanguageRepositoryInterface
     */
    private $languageRepository;

    /**
     * NotificationContentProvider constructor.
     * @param array                       $predefinedMessages
     * @param CarrierRepositoryInterface  $carrierProvider
     * @param LanguageRepositoryInterface $languageRepository
     */
    public function __construct(
        array $predefinedMessages,
        CarrierRepositoryInterface $carrierProvider,
        LanguageRepositoryInterface $languageRepository
    )
    {
        $this->hardcodedMessages  = $predefinedMessages;
        $this->carrierProvider    = $carrierProvider;
        $this->languageRepository = $languageRepository;
    }


    public function getSMSText(
        string $notificationType,
        string $messageNamespace,
        SubscriptionPack $subscriptionPack,
        LanguageInterface $language
    ): string
    {
        $str = $textFromSubscriptionPack = $this->extractFromSubscriptionPack($notificationType, $subscriptionPack);
        if (trim($str)) {
            return $textFromSubscriptionPack;
        }

        if (!$messageNamespace) {
            throw new MissingSMSTextException(sprintf('Both `message namespace` and `Subscription Pack texts` are not exist.'));
        }


        $code = $language->getCode();
        if (!isset($this->hardcodedMessages[$messageNamespace][$code])) {
            throw new MissingSMSTextException(sprintf('Text for %s namespace is missing', $messageNamespace));
        }

        if (!isset($this->hardcodedMessages[$messageNamespace][$code][$notificationType])) {
            throw new MissingSMSTextException(sprintf('Text for %s notification type is missing', $notificationType));
        }

        return $this->hardcodedMessages[$messageNamespace][$code][$notificationType];
    }

    private function extractFromSubscriptionPack($notificationType, SubscriptionPack $subscriptionPack)
    {
        if ($notificationType === 'subscribe') {
            return $subscriptionPack->getWelcomeSMSText();
        }

        if ($notificationType === 'unsubscribe') {
            return $subscriptionPack->getUnsubscribeSMSText();
        }

        if ($notificationType === 'renewing') {
            return $subscriptionPack->getRenewalSMSText();
        }
    }

}