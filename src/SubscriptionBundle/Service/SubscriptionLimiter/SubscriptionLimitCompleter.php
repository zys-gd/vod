<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionLimitCompleter
{
    /**
     * @var SubscriptionLimiterInterface
     */
    private $subscriptionLimiter;

    /**
     * SubscriptionLimitCompleter constructor.
     *
     * @param SubscriptionLimiterInterface $subscriptionLimiter
     */
    public function __construct(SubscriptionLimiterInterface $subscriptionLimiter)
    {
        $this->subscriptionLimiter = $subscriptionLimiter;
    }

    /**
     * @param ProcessResult    $response
     * @param SessionInterface $session
     */
    public function finishProcess(ProcessResult $response, SessionInterface $session): void
    {
        if ($response->isSuccessful()) {
            $this->subscriptionLimiter->finishLimitingProcess($session);
        }

        if ($response->isFailed()) {
            switch ($response->getError()) {
                case 'not_enough_credit':
                    //TODO: remove?
                    break;
                default:
                    $this->subscriptionLimiter->cancelLimitingProcess($session);
            }
        }
    }
}