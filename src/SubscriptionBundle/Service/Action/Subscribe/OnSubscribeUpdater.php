<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:36
 */

namespace SubscriptionBundle\Service\Action\Subscribe;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Event\SubscriptionSubscribeEvent;
use SubscriptionBundle\Service\Action\Common\CommonSubscriptionUpdater;
use SubscriptionBundle\Service\CreditsCalculator;
use SubscriptionBundle\Service\RenewDateCalculator;
use SubscriptionBundle\Service\SubscriptionProvider;

class OnSubscribeUpdater
{
    /**
     * @var SubscriptionProvider
     */
    private $subscriptionProvider;
    /**
     * @var CreditsCalculator
     */
    private $creditsCalculator;
    /**
     * @var \SubscriptionBundle\Service\Action\Common\\SubscriptionBundle\Service\RenewDateCalculator
     */
    private $renewDateCalculator;
    /**
     * @var CommonSubscriptionUpdater
     */
    private $commonSubscriptionUpdater;
    private $eventDispatcher;


    /**
     * OnSubscribeUpdater constructor.
     * @param SubscriptionProvider                                      $subscriptionProvider
     * @param CreditsCalculator                                         $creditsCalculator
     * @param \SubscriptionBundle\Service\RenewDateCalculator $renewDateCalculator
     * @param EventDispatcherInterface                                  $eventDispatcher
     * @param CommonSubscriptionUpdater                                 $commonSubscriptionUpdater
     */
    public function __construct(
        SubscriptionProvider $subscriptionProvider,
        CreditsCalculator $creditsCalculator,
        RenewDateCalculator $renewDateCalculator,
        EventDispatcherInterface $eventDispatcher,
        CommonSubscriptionUpdater $commonSubscriptionUpdater
    )
    {
        $this->subscriptionProvider      = $subscriptionProvider;
        $this->creditsCalculator         = $creditsCalculator;
        $this->renewDateCalculator       = $renewDateCalculator;
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

    public function updateSubscriptionByCallbackResponse(Subscription $subscription, ProcessResult $response)
    {
        if ($response->isSuccessful()) {
            $this->applySuccess($subscription);
        }

        $this->commonSubscriptionUpdater->updateSubscriptionByCallbackResponse($subscription, $response);

        if ($response->isFailed()) {

            $subscription->setError($response->getError());

            switch ($response->getError()) {
                case 'not_enough_credit':
                    $subscription->setStatus(Subscription::IS_ON_HOLD);
                    if ($subscription->getCredits() >= 2) {
                        $subscription->setCredits($subscription->getCredits() - 2);
                    }
                    break;
                default:
                    $this->applyFailure($subscription, $response->getError());
            }
        }
    }

    /**
     * @param Subscription $subscription
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function applySuccess(Subscription $subscription)
    {
        $subscription->setStatus(Subscription::IS_ACTIVE);

        $renewDate = $this->renewDateCalculator->calculateRenewDate($subscription);
        $subscription->setRenewDate($renewDate);

        if (intval($subscription->getCredits()) === 0) {
            $billableUser         = $subscription->getUser();
            $existingSubscription = $this->subscriptionProvider->getExistingSubscriptionForUser($billableUser);

            $newCredits = $this->creditsCalculator->calculateCredits($subscription, $subscription->getSubscriptionPack(), $existingSubscription);
            $subscription->setCredits($newCredits);
        }

        $this->callSubscriptionSubscribeEvent($subscription);
    }

    /**
     * @param Subscription $subscription
     */
    private function callSubscriptionSubscribeEvent(Subscription $subscription)
    {
        $event = new SubscriptionSubscribeEvent($subscription);
        $this->eventDispatcher->dispatch(SubscriptionSubscribeEvent::EVENT_NAME, $event);
    }

    protected function applyFailure(Subscription $subscription, string $errorName)
    {
        $subscription->setStatus(Subscription::IS_ERROR);
    }

}