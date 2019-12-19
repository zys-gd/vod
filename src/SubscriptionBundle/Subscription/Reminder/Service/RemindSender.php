<?php

namespace SubscriptionBundle\Subscription\Reminder\Service;

use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\Entity\Subscription;
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

    public function send(Subscription $subscription, string $body): bool
    {

    }
}