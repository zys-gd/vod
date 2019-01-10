<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.10.18
 * Time: 15:41
 */

namespace SubscriptionBundle\Service\Notification\Common;


use AppBundle\Service\Domain\Carrier\CarrierProvider;
use SubscriptionBundle\BillingFramework\Listener\NotificationContentProvider;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Service\Notification\Common\SMSTexts\MessageKeyHandlerProvider;
use SubscriptionBundle\Entity\SubscriptionPack;

class SMSTextProvider
{
    /**
     * @var array
     */
    private $hardcodedMessages;
    /**
     * @var CarrierProvider
     */
    private $carrierProvider;

    /**
     * NotificationContentProvider constructor.
     * @param array           $predefinedMessages
     * @param CarrierProvider $carrierProvider
     */
    public function __construct(array $predefinedMessages, CarrierProvider $carrierProvider)
    {
        $this->hardcodedMessages = $predefinedMessages;
        $this->carrierProvider   = $carrierProvider;
    }


    public function getSMSText(string $notificationType, string $groupName, SubscriptionPack $subscriptionPack): string
    {
        if ($textFromSubscriptionPack = $this->extractFromSubscriptionPack($notificationType, $subscriptionPack)) {
            return $textFromSubscriptionPack;
        }

        $carrier = $this->carrierProvider->getCarrierEntity($subscriptionPack->getCarrierId());

        $namespaceProvider   = MessageKeyHandlerProvider::getService($groupName);
        $messageNamespace = $namespaceProvider->getKey(
            $carrier->getIdCarrier(),
            ''
        );

        if (!isset($this->hardcodedMessages[$messageNamespace]['message'])) {
            throw new MissingSMSTextException(sprintf('Text for %s namespace is missing', $messageNamespace));
        }

        if (!isset($this->hardcodedMessages[$messageNamespace]['message'][$notificationType])) {
            throw new MissingSMSTextException(sprintf('Text for %s notification type is missing', $notificationType));
        }

        return $this->hardcodedMessages[$messageNamespace]['message'][$notificationType];
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