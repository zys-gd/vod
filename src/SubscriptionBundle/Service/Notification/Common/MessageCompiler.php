<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 28.10.2018
 * Time: 16:00
 */

namespace SubscriptionBundle\Service\Notification\Common;


use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\NotificationMessage;
use SubscriptionBundle\BillingFramework\Notification\API\MessageCreator;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\Notification\Common\SMSTexts\MessageKeyHandlerProvider;

class MessageCompiler
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SMSTextProvider
     */
    private $SMSTextProvider;
    /**
     * @var MessageCreator
     */
    private $messageCreator;

    /**
     * MessageCompiler constructor.
     * @param LoggerInterface $logger
     * @param MessageCreator  $messageCreator
     * @param SMSTextProvider $SMSTextProvider
     */
    public function __construct(
        LoggerInterface $logger,
        MessageCreator $messageCreator,
        SMSTextProvider $SMSTextProvider
    )
    {
        $this->logger          = $logger;
        $this->SMSTextProvider = $SMSTextProvider;
        $this->messageCreator  = $messageCreator;
    }


    /**
     * @param string           $type
     * @param User             $User
     * @param SubscriptionPack $subscriptionPack
     * @param Subscription     $subscription
     * @param null             $billingProcessId
     * @param array            $bodyVariables
     * @return NotificationMessage
     * @throws MissingSMSTextException
     */
    public function compileNotification(
        string $type,
        User $User,
        SubscriptionPack $subscriptionPack,
        Subscription $subscription,
        $billingProcessId = null,
        array $bodyVariables = []
    ): NotificationMessage
    {

        //TODO Maybe we may move sms text resolving upper?
        try {
            $body = $this->SMSTextProvider->getSMSText($type, MessageKeyHandlerProvider::TYPE_SUBSCRIBE_3G, $subscriptionPack);
        } catch (MissingSMSTextException $exception) {
            $this->logger->error($exception->getMessage(), ['pack' => $subscriptionPack]);
            throw  $exception;
        }

        $identifier = $User->getIdentifier();
        $operatorId = $User->getCarrier()->getOperatorId();

        $smsVariables = array_merge(
            $this->getDefaultSMSVariables($subscriptionPack, $subscription),
            $bodyVariables
        );
        $body         = $this->compileSmsTextTemplate($smsVariables, $body);
        $message      = $this->messageCreator->createMessage($identifier, $type, $body, $operatorId);

        if ($billingProcessId) {
            $message->setBillingProccess($billingProcessId);
        }


        return $message;
    }

    /**
     * @param array $bodyVariables
     * @param       $body
     * @return string|string[]|null
     */
    private function compileSmsTextTemplate(array $bodyVariables, string $body): string
    {
        foreach ($bodyVariables as $key => $value) {
            $body = str_replace($key, $value, $body);
        }
        return $body;
    }

    private function getDefaultSMSVariables(SubscriptionPack $pack, Subscription $subscription): array
    {
        return [
            '_price_'      => $pack->getTierPrice(),
            '_currency_'   => $pack->getTierCurrency(),
            '_home_url_'   => 'home',
            '_unsub_url_'  => 'myaccount',
            '_renew_date_' => $subscription->getRenewDate()->format(DATE_ISO8601)
        ];

    }
}