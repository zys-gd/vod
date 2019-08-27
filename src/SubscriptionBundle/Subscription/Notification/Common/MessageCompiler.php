<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 28.10.2018
 * Time: 16:00
 */

namespace SubscriptionBundle\Subscription\Notification\Common;


use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\NotificationMessage;
use SubscriptionBundle\BillingFramework\Notification\API\MessageCreator;
use SubscriptionBundle\Subscription\Notification\SMSText\SMSTextProvider;

class MessageCompiler
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var \SubscriptionBundle\Subscription\Notification\SMSText\SMSTextProvider
     */
    private $SMSTextProvider;
    /**
     * @var MessageCreator
     */
    private $messageCreator;

    /**
     * MessageCompiler constructor.
     * @param LoggerInterface                                                       $logger
     * @param MessageCreator                                                        $messageCreator
     * @param \SubscriptionBundle\Subscription\Notification\SMSText\SMSTextProvider $SMSTextProvider
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
     * @param string $type
     * @param User   $User
     * @param string $template
     * @param null   $billingProcessId
     * @param array  $bodyVariables
     * @return NotificationMessage
     */
    public function compileNotification(
        string $type,
        User $User,
        string $template,
        $billingProcessId = null,
        array $bodyVariables = []
    ): NotificationMessage
    {

        $identifier = $User->getIdentifier();
        $operatorId = $User->getCarrier()->getOperatorId();
        $body       = $this->compileSmsTextTemplate($bodyVariables, $template);
        $message    = $this->messageCreator->createMessage($identifier, $type, $body, $operatorId);

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

}