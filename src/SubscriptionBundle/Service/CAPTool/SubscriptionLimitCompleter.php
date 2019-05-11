<?php


namespace SubscriptionBundle\Service\CAPTool;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

class SubscriptionLimitCompleter
{
    /**
     * @var SubscriptionLimiterInterface
     */
    private $subscriptionLimiter;

    /**
     * SubscriptionLimitCompleter constructor.
     *
     * @param SubscriptionLimiter $subscriptionLimiter
     */
    public function __construct(SubscriptionLimiter $subscriptionLimiter)
    {
        $this->subscriptionLimiter = $subscriptionLimiter;
    }

    /**
     * @param ProcessResult $response
     * @param Subscription  $subscription
     */
    public function finishProcess(ProcessResult $response, Subscription $subscription): void
    {
        $user = $subscription->getUser();

        if ($response->isSuccessful() && $response->isFinal()) {
            $this->subscriptionLimiter->finishSubscription($user->getCarrier(), $subscription);
        }

        if ($response->isFailed()) {
            switch ($response->getError()) {
                case 'not_enough_credit':
                    //TODO: remove?
                    break;
                default:
                    $this->subscriptionLimiter->releasePendingSlot($user->getCarrier());
            }
        }
    }
}