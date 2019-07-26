<?php

namespace SubscriptionBundle\Subscription\Notification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\SMSRequest;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Notification\Common\DefaultSMSVariablesProvider;
use SubscriptionBundle\Subscription\Notification\Common\MessageCompiler;
use SubscriptionBundle\Subscription\Notification\Common\ProcessIdExtractor;
use SubscriptionBundle\Subscription\Notification\Common\SMSTextProvider;
use SubscriptionBundle\Subscription\Notification\Impl\NotificationHandlerProvider;

/**
 * Class Notifier
 */
class Notifier
{
    /**
     * @var MessageCompiler
     */
    private $messageCompiler;
    /**
     * @var RequestSender
     */
    private $sender;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProcessIdExtractor
     */
    private $processIdExtractor;
    /**
     * @var NotificationHandlerProvider
     */
    private $notificationHandlerProvider;
    /**
     * @var DefaultSMSVariablesProvider
     */
    private $defaultSMSVariablesProvider;
    /**
     * @var SMSTextProvider
     */
    private $SMSTextProvider;

    /**
     * Notifier constructor.
     * @param MessageCompiler             $messageCompiler
     * @param RequestSender               $sender
     * @param LoggerInterface             $logger
     * @param ProcessIdExtractor          $processIdExtractor
     * @param NotificationHandlerProvider $notificationHandlerProvider
     * @param DefaultSMSVariablesProvider $defaultSMSVariablesProvider
     * @param SMSTextProvider             $SMSTextProvider
     */
    public function __construct(
        MessageCompiler $messageCompiler,
        RequestSender $sender,
        LoggerInterface $logger,
        ProcessIdExtractor $processIdExtractor,
        NotificationHandlerProvider $notificationHandlerProvider,
        DefaultSMSVariablesProvider $defaultSMSVariablesProvider,
        SMSTextProvider $SMSTextProvider
    ) {
        $this->messageCompiler             = $messageCompiler;
        $this->sender                      = $sender;
        $this->logger                      = $logger;
        $this->processIdExtractor          = $processIdExtractor;
        $this->notificationHandlerProvider = $notificationHandlerProvider;
        $this->defaultSMSVariablesProvider = $defaultSMSVariablesProvider;
        $this->SMSTextProvider             = $SMSTextProvider;
    }


    public function sendNotification(
        string $processType,
        Subscription $subscription,
        SubscriptionPack $subscriptionPack,
        CarrierInterface $carrier
    )
    {
        try {
            $handler = $this->notificationHandlerProvider->get($processType, $carrier);

            if (!$handler->isNotificationShouldBeSent()) {
                return;
            }

            $User = $subscription->getUser();

            if ($handler->isProcessIdUsedInNotification()) {
                $processId = $this->processIdExtractor->extractProcessId($User);
            } else {
                $processId = null;
            }

            $variables = $this->defaultSMSVariablesProvider->getDefaultSMSVariables(
                $subscriptionPack,
                $subscription,
                $User
            );

            try {
                $body = $this->SMSTextProvider->getSMSText(
                    $processType,
                    (string)$handler->getMessageNamespace(),
                    $subscriptionPack,
                    $handler->getSmsLanguage()
                );
            } catch (MissingSMSTextException $exception) {
                $this->logger->error($exception->getMessage(), ['pack' => $subscriptionPack]);
                throw  $exception;
            }


            $notification = $this->messageCompiler->compileNotification(
                $processType,
                $User,
                $body,
                $processId,
                $variables
            );

            $this->sender->sendNotification($notification, $carrier->getBillingCarrierId());


        } catch (MissingSMSTextException $exception) {
            $this->logger->error('Missing sms text');
        }

    }

    public function sendSMS(CarrierInterface $carrier, string $clientUser, string $urlId, string $subscriptionPlan, string $lang)
    {
        $request = new SMSRequest($clientUser, $urlId, $subscriptionPlan, $lang);

        $this->sender->sendSMS($request, $carrier->getBillingCarrierId());

    }
}