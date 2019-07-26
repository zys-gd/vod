<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:36
 */

namespace SubscriptionBundle\Subscription\Renew;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\CommonSubscriptionUpdater;
use SubscriptionBundle\Subscription\Subscribe\Service\CreditsCalculator;
use SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OnRenewUpdater
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var \SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator
     */
    private $renewDateCalculator;
    /**
     * @var \SubscriptionBundle\Subscription\Common\SubscriptionExtractor
     */
    private $subscriptionProvider;
    /**
     * @var \SubscriptionBundle\Subscription\Renew\Common\\SubscriptionBundle\Subscription\Subscribe\Service\CreditsCalculator
     */
    private $creditsCalculator;
    /**
     * @var CommonSubscriptionUpdater
     */
    private $commonSubscriptionUpdater;

    /**
     * OnRenewUpdater constructor.
     *
     * @param \SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator   $renewDateCalculator
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionExtractor        $subscriptionProvider
     * @param \SubscriptionBundle\Subscription\Subscribe\Service\CreditsCalculator $creditsCalculator
     * @param EventDispatcherInterface                                             $eventDispatcher
     * @param \SubscriptionBundle\Subscription\Common\CommonSubscriptionUpdater    $commonSubscriptionUpdater
     */
    public function __construct(
        RenewDateCalculator $renewDateCalculator,
        SubscriptionExtractor $subscriptionProvider,
        CreditsCalculator $creditsCalculator,
        EventDispatcherInterface $eventDispatcher,
        \SubscriptionBundle\Subscription\Common\CommonSubscriptionUpdater $commonSubscriptionUpdater
    )
    {
        $this->renewDateCalculator       = $renewDateCalculator;
        $this->subscriptionProvider      = $subscriptionProvider;
        $this->creditsCalculator         = $creditsCalculator;
        $this->eventDispatcher           = $eventDispatcher;
        $this->commonSubscriptionUpdater = $commonSubscriptionUpdater;
    }

    /**
     * @param Subscription  $subscription
     * @param ProcessResult $processResponse
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateSubscriptionByResponse(Subscription $subscription, ProcessResult $processResponse)
    {
        $this->updateSubscriptionByCallbackResponse($subscription, $processResponse);

        if ($processResponse->isRedirectRequired()) {
            $subscription->setRedirectUrl($processResponse->getUrl());
        }
    }

    /**
     * @param Subscription  $subscription
     * @param ProcessResult $processResponse
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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
                    break;
                default:
                    $this->applyFailure($subscription, $processResponse->getError());
            }
        }
    }

    /**
     * @param Subscription $subscription
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function applySuccess(Subscription $subscription)
    {
        $subscription->setStatus(Subscription::IS_ACTIVE);
        $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
        $subscription->setError('');
        $subscription->setRenewDate($this->renewDateCalculator->calculateRenewDate($subscription));

        $User                 = $subscription->getUser();
        $existingSubscription = $this->subscriptionProvider->getExistingSubscriptionForUser($User);

        $newCredits = $this->creditsCalculator->calculateCredits($subscription, $subscription->getSubscriptionPack(), $existingSubscription);
        $subscription->setCredits($newCredits);
    }

    protected function applyFailure(Subscription $subscription, string $errorName)
    {
        switch ($errorName) {
            case 'batch_limit_exceeded':
            case 'canceled':
                $subscription->setStatus(Subscription::IS_INACTIVE);
                $subscription->setCurrentStage(Subscription::ACTION_UNSUBSCRIBE);
                break;
            default:
                $subscription->setStatus(Subscription::IS_ERROR);
        }


    }

    /**
     * @param Subscription $subscription
     * @param int          $processId
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateSubscriptionOnSuccess(Subscription $subscription, int $processId)
    {
        $this->applySuccess($subscription);

    }

    public function updateSubscriptionOnFailure(Subscription $subscription, string $errorName)
    {
        $this->applyFailure($subscription, $errorName);
    }
}