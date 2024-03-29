<?php

namespace SubscriptionBundle\Subscription\Notification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Repository\Interfaces\LanguageRepositoryInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\SMSRequest;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Notification\Common\DefaultSMSVariablesProvider;
use SubscriptionBundle\Subscription\Notification\Common\MessageCompiler;
use SubscriptionBundle\Subscription\Notification\Common\ProcessIdExtractor;
use SubscriptionBundle\Subscription\Notification\Impl\HasCustomResponseProcessing;
use SubscriptionBundle\Subscription\Notification\Impl\NotificationHandlerProvider;
use SubscriptionBundle\Subscription\Notification\SMSText\SMSTextProvider;

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
     * @var LanguageRepositoryInterface
     */
    private $repository;

    /**
     * Notifier constructor.
     * @param MessageCompiler                                                       $messageCompiler
     * @param RequestSender                                                         $sender
     * @param LoggerInterface                                                       $logger
     * @param ProcessIdExtractor                                                    $processIdExtractor
     * @param NotificationHandlerProvider                                           $notificationHandlerProvider
     * @param DefaultSMSVariablesProvider                                           $defaultSMSVariablesProvider
     * @param \SubscriptionBundle\Subscription\Notification\SMSText\SMSTextProvider $SMSTextProvider
     * @param LanguageRepositoryInterface                                           $repository
     */
    public function __construct(
        MessageCompiler $messageCompiler,
        RequestSender $sender,
        LoggerInterface $logger,
        ProcessIdExtractor $processIdExtractor,
        NotificationHandlerProvider $notificationHandlerProvider,
        DefaultSMSVariablesProvider $defaultSMSVariablesProvider,
        SMSTextProvider $SMSTextProvider,
        LanguageRepositoryInterface $repository
    )
    {
        $this->messageCompiler             = $messageCompiler;
        $this->sender                      = $sender;
        $this->logger                      = $logger;
        $this->processIdExtractor          = $processIdExtractor;
        $this->notificationHandlerProvider = $notificationHandlerProvider;
        $this->defaultSMSVariablesProvider = $defaultSMSVariablesProvider;
        $this->SMSTextProvider             = $SMSTextProvider;
        $this->repository                  = $repository;
    }


    public function sendNotification(
        string $processType,
        Subscription $subscription,
        SubscriptionPack $subscriptionPack,
        CarrierInterface $carrier
    ): bool
    {
        $handler = $this->notificationHandlerProvider->get($processType, $carrier);

        if (!$handler->isNotificationShouldBeSent()) {
            return true;
        }

        $user = $subscription->getUser();

        if ($handler->isProcessIdUsedInNotification()) {
            $processId = $this->processIdExtractor->extractProcessId($user);
        } else {
            $processId = null;
        }

        $variables = $this->defaultSMSVariablesProvider->getDefaultSMSVariables(
            $subscriptionPack,
            $subscription,
            $user
        );


        $isUserLocaleCanBeUsed = true;
        $userLanguage          = $user->getLanguageCode();
        $smsLanguage           = $this->repository->findByCode($userLanguage);

        if ($smsLanguage) {
            try {
                $body = $this->SMSTextProvider->getSMSText(
                    $processType,
                    $carrier,
                    $subscriptionPack,
                    $smsLanguage
                );
            } catch (MissingSMSTextException $exception) {
                $isUserLocaleCanBeUsed = false;
            }
        } else {
            $isUserLocaleCanBeUsed = false;
        }

        if (!$isUserLocaleCanBeUsed) {
            try {
                $body = $this->SMSTextProvider->getSMSText(
                    $processType,
                    $carrier,
                    $subscriptionPack,
                    $handler->getSmsLanguage()
                );

            } catch (MissingSMSTextException $exception) {
                $this->logger->error($exception->getMessage(), ['pack' => $subscriptionPack]);
                throw $exception;
            }
        }


        $notification = $this->messageCompiler->compileNotification(
            $processType,
            $user,
            $body,
            $processId,
            $variables
        );

        $result = $this->sender->sendNotification($notification, $carrier->getBillingCarrierId());

        if ($handler instanceof HasCustomResponseProcessing) {
            return $handler->isResponseOk($result);
        } else {
            return $this->isResponseOk($result);
        }


    }

    public function sendSMS(CarrierInterface $carrier, string $clientUser, string $urlId, string $subscriptionPlan, string $lang)
    {
        $request = new SMSRequest($clientUser, $urlId, $subscriptionPlan, $lang);

        $this->sender->sendSMS($request, $carrier->getBillingCarrierId());

    }

    /**
     * @param $result
     * @return bool
     */
    private function isResponseOk($result): bool
    {
        return
            !is_null($result) &&
            !(is_array($result) && $result['provider_fields']['error']);
    }
}