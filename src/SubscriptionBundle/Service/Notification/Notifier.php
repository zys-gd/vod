<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 28.10.2018
 * Time: 16:46
 */

namespace SubscriptionBundle\Service\Notification;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\LanguageRepositoryInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\SMSRequest;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\Notification\Common\DefaultSMSVariablesProvider;
use SubscriptionBundle\Service\Notification\Common\MessageCompiler;
use SubscriptionBundle\Service\Notification\Common\ProcessIdExtractor;
use SubscriptionBundle\Service\Notification\Common\ShortUrlHashGenerator;
use SubscriptionBundle\Service\Notification\Common\SMSTextProvider;
use SubscriptionBundle\Service\Notification\Impl\NotificationHandlerProvider;

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
     * @var ShortUrlHashGenerator
     */
    private $shortUrlHashGenerator;

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
     * @param ShortUrlHashGenerator       $shortUrlHashGenerator
     * @param DefaultSMSVariablesProvider $defaultSMSVariablesProvider
     * @param SMSTextProvider             $SMSTextProvider
     * @param LanguageRepositoryInterface $languageRepository
     */
    public function __construct(
        MessageCompiler $messageCompiler,
        RequestSender $sender,
        LoggerInterface $logger,
        ProcessIdExtractor $processIdExtractor,
        NotificationHandlerProvider $notificationHandlerProvider,
        ShortUrlHashGenerator $shortUrlHashGenerator,
        DefaultSMSVariablesProvider $defaultSMSVariablesProvider,
        SMSTextProvider $SMSTextProvider
    )
    {
        $this->messageCompiler             = $messageCompiler;
        $this->sender                      = $sender;
        $this->logger                      = $logger;
        $this->processIdExtractor          = $processIdExtractor;
        $this->notificationHandlerProvider = $notificationHandlerProvider;
        $this->shortUrlHashGenerator       = $shortUrlHashGenerator;
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


            if (!$User->getUrlId()) {
                $User->setShortUrlId($this->shortUrlHashGenerator->generate());
                $this->logger->debug('Generated auto-login URL for user. ', ['url' => $User->getUrlId()]);
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