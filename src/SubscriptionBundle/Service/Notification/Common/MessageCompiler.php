<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 28.10.2018
 * Time: 16:00
 */

namespace SubscriptionBundle\Service\Notification\Common;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\NotificationMessage;
use SubscriptionBundle\BillingFramework\Notification\API\MessageCreator;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Service\Notification\Common\SMSTexts\MessageKeyHandlerProvider;
use SubscriptionBundle\Entity\SubscriptionPack;
use UserBundle\Entity\BillableUser;

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
        $this->logger = $logger;
        $this->SMSTextProvider = $SMSTextProvider;
        $this->messageCreator = $messageCreator;
    }


    /**
     * @param string $type
     * @param BillableUser $billableUser
     * @param SubscriptionPack $subscriptionPack
     * @param null $billingProcessId
     * @param array $bodyVariables
     * @return NotificationMessage
     * @throws MissingSMSTextException
     */
    public function compileNotification(
        string $type,
        BillableUser $billableUser,
        SubscriptionPack $subscriptionPack,
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

        $identifier = $billableUser->getIdentifier();
        $operatorId = $billableUser->getCarrier()->getOperatorId();

        $body = $this->compileSmsTextTemplate($bodyVariables, $body);
        $message = $this->messageCreator->createMessage($identifier, $type, $body, $operatorId);

        if ($billingProcessId) {
            $message->setBillingProccess($billingProcessId);
        }


        return $message;
    }

    /**
     * @param array $bodyVariables
     * @param $body
     * @return string|string[]|null
     */
    private function compileSmsTextTemplate(array $bodyVariables, string $body): string
    {
        foreach ($bodyVariables as $key => $value) {
            $body = str_replace($key, $value, $body);
        }
        return $body;
    }
}