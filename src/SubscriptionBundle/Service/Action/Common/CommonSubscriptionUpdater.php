<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:35
 */

namespace SubscriptionBundle\Service\Action\Common;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

class CommonSubscriptionUpdater
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * CommonSubscriptionUpdater constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }



    // TODO no better way? Rework if possible.
    // TODO remove code duplication atleast.
    final public function updateSubscriptionByCallbackResponse(Subscription $subscription, ProcessResult $processResponse)
    {
        /*if ($processResponse->getStatusCode() == 400) {
            $subscription->setStatus(Subscription::IS_PENDING);
        }*/

        if ($processResponse->isPutOnHold()) {
            $subscription->setCredits(0);
            $subscription->setStatus(Subscription::IS_ON_HOLD);
        }

    }

}