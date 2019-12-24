<?php

namespace SubscriptionBundle\Subscription\Reminder\Service;

use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Notification\Common\DefaultSMSVariablesProvider;
use SubscriptionBundle\Subscription\Notification\Common\MessageCompiler;

/**
 * Class RemindSender
 */
class RemindSender
{
    /**
     * @var RequestSender
     */
    private $requestSender;

    /**
     * @var MessageCompiler
     */
    private $messageCompiler;

    /**
     * @var DefaultSMSVariablesProvider
     */
    private $variablesProvider;

    /**
     * RemindSender constructor.
     *
     * @param RequestSender               $requestSender
     * @param MessageCompiler             $messageCompiler
     * @param DefaultSMSVariablesProvider $variablesProvider
     */
    public function __construct(
        RequestSender $requestSender,
        MessageCompiler $messageCompiler,
        DefaultSMSVariablesProvider $variablesProvider
    ) {
        $this->requestSender = $requestSender;
        $this->messageCompiler = $messageCompiler;
        $this->variablesProvider = $variablesProvider;
    }

    /**
     * @param User             $user
     * @param SubscriptionPack $subscriptionPack
     * @param Subscription     $subscription
     * @param string           $body
     *
     * @return bool
     */
    public function send(User $user, SubscriptionPack $subscriptionPack, Subscription $subscription, string $body): bool
    {
        $variables = $this->variablesProvider->getDefaultSMSVariables($subscriptionPack, $subscription, $user);

        $notification = $this->messageCompiler->compileNotification(
            'reminder',
            $user,
            $body,
            null,
            $variables
        );

        $result = $this->requestSender->sendNotification($notification, $user->getCarrierId());
        var_dump($result);

        return true;
    }
}