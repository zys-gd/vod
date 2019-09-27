<?php


namespace SubscriptionBundle\CAPTool\Subscription;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;

class SubscriptionLimitCompleter
{
    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;

    /**
     * SubscriptionLimitCompleter constructor.
     *
     * @param SubscriptionLimiter         $subscriptionLimiter
     * @param ProcessResultSuccessChecker $resultSuccessChecker
     */
    public function __construct(SubscriptionLimiter $subscriptionLimiter, ProcessResultSuccessChecker $resultSuccessChecker)
    {
        $this->subscriptionLimiter  = $subscriptionLimiter;
        $this->resultSuccessChecker = $resultSuccessChecker;
    }

    /**
     * @param ProcessResult $response
     * @param Subscription  $subscription
     */
    public function finishProcess(ProcessResult $response, Subscription $subscription): void
    {
        $user = $subscription->getUser();

        if ($this->resultSuccessChecker->isSuccessful($response)) {
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