<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:36
 */

namespace SubscriptionBundle\Service\Action\Renew;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Event\SubscriptionOnHoldEvent;
use SubscriptionBundle\Service\Action\Common\CommonSubscriptionUpdater;
use SubscriptionBundle\Service\CreditsCalculator;
use SubscriptionBundle\Service\RenewDateCalculator;
use SubscriptionBundle\Service\SubscriptionProvider;

class OnRenewUpdater
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var \SubscriptionBundle\Service\RenewDateCalculator
     */
    private $renewDateCalculator;
    /**
     * @var SubscriptionProvider
     */
    private $subscriptionProvider;
    /**
     * @var CreditsCalculator
     */
    private $creditsCalculator;
    /**
     * @var CommonSubscriptionUpdater
     */
    private $commonSubscriptionUpdater;

    /**
     * OnRenewUpdater constructor.
     * @param \SubscriptionBundle\Service\RenewDateCalculator                     $renewDateCalculator
     * @param SubscriptionProvider                                                          $subscriptionProvider
     * @param \SubscriptionBundle\Service\CreditsCalculator                $creditsCalculator
     * @param EventDispatcherInterface                                                      $eventDispatcher
     * @param \SubscriptionBundle\Service\Action\Common\CommonSubscriptionUpdater $commonSubscriptionUpdater
     */
    public function __construct(
        RenewDateCalculator $renewDateCalculator,
        SubscriptionProvider $subscriptionProvider,
        CreditsCalculator $creditsCalculator,
        EventDispatcherInterface $eventDispatcher,
        \SubscriptionBundle\Service\Action\Common\CommonSubscriptionUpdater $commonSubscriptionUpdater
    )
    {
        $this->renewDateCalculator       = $renewDateCalculator;
        $this->subscriptionProvider      = $subscriptionProvider;
        $this->creditsCalculator         = $creditsCalculator;
        $this->eventDispatcher           = $eventDispatcher;
        $this->commonSubscriptionUpdater = $commonSubscriptionUpdater;
    }

    public function updateSubscriptionByResponse(Subscription $subscription, ProcessResult $processResponse)
    {
        $this->updateSubscriptionByCallbackResponse($subscription, $processResponse);

        if ($processResponse->isRedirectRequired()) {
            $subscription->setRedirectUrl($processResponse->getUrl());
        }
    }

    final public function updateSubscriptionByCallbackResponse(Subscription $subscription, ProcessResult $processResponse)
    {

        if ($processResponse->isSuccessful()) {
            $this->applySuccess($subscription);
        }

        $this->commonSubscriptionUpdater->updateSubscriptionByCallbackResponse($subscription, $processResponse);

        if ($processResponse->isFailed()) {

            $subscription->setError($processResponse->getError());

            switch ($processResponse->getError()) {
                case 'not_enough_credit':
                    $subscription->setStatus(Subscription::IS_ON_HOLD);
                    if ($subscription->getCredits() >= 2) {
                        $subscription->setCredits($subscription->getCredits() - 2);
                    }

                    $this->callSubscriptionOnHoldEvent($subscription);
                    break;
                default:
                    $this->applyFailure($subscription, $processResponse->getError());
            }
        }
    }

    /**
     * @param Subscription $subscription
     */
    protected function applySuccess(Subscription $subscription)
    {

        $subscription->setStatus(Subscription::IS_ACTIVE);
        $subscription->setRenewDate($this->renewDateCalculator->calculateRenewDate($subscription));

        $billableUser         = $subscription->getUser();
        $existingSubscription = $this->subscriptionProvider->getExistingSubscriptionForBillableUser($billableUser);

        $newCredits = $this->creditsCalculator->calculateCredits($subscription, $subscription->getSubscriptionPack(), $existingSubscription);
        $subscription->setCredits($newCredits);
    }

    /**
     * @param Subscription $subscription
     */
    private function callSubscriptionOnHoldEvent(Subscription $subscription)
    {
        $event = new SubscriptionOnHoldEvent($subscription);
        $this->eventDispatcher->dispatch(SubscriptionOnHoldEvent::EVENT_NAME, $event);
    }

    protected function applyFailure(Subscription $subscription, string $errorName)
    {
        switch ($errorName) {
            case 'batch_limit_exceeded':
                $subscription->setStatus(Subscription::IS_ERROR);
                break;
            default:
                $subscription->setStatus(Subscription::IS_INACTIVE);
        }


    }
}