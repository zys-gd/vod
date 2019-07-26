<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:36
 */

namespace SubscriptionBundle\Subscription\Subscribe;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\CommonSubscriptionUpdater;
use SubscriptionBundle\CAPTool\DTO\CarrierLimiterData;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimiter;
use SubscriptionBundle\Subscription\Subscribe\Service\CreditsCalculator;
use SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OnSubscribeUpdater
{
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionProvider;
    /**
     * @var \SubscriptionBundle\Subscription\Renew\Common\\SubscriptionBundle\Subscription\Subscribe\Service\CreditsCalculator
     */
    private $creditsCalculator;
    /**
     * @var \SubscriptionBundle\Subscription\Common\\SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator
     */
    private $renewDateCalculator;
    /**
     * @var CommonSubscriptionUpdater
     */
    private $commonSubscriptionUpdater;
    private $eventDispatcher;
    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;


    /**
     * OnSubscribeUpdater constructor.
     *
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionExtractor        $subscriptionProvider
     * @param \SubscriptionBundle\Subscription\Subscribe\Service\CreditsCalculator $creditsCalculator
     * @param \SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator   $renewDateCalculator
     * @param EventDispatcherInterface                                             $eventDispatcher
     * @param CommonSubscriptionUpdater                                            $commonSubscriptionUpdater
     * @param SubscriptionLimiter                                                  $subscriptionLimiter
     */
    public function __construct(
        SubscriptionExtractor $subscriptionProvider,
        CreditsCalculator $creditsCalculator,
        RenewDateCalculator $renewDateCalculator,
        EventDispatcherInterface $eventDispatcher,
        CommonSubscriptionUpdater $commonSubscriptionUpdater,
        SubscriptionLimiter $subscriptionLimiter
    )
    {
        $this->subscriptionProvider      = $subscriptionProvider;
        $this->creditsCalculator         = $creditsCalculator;
        $this->renewDateCalculator       = $renewDateCalculator;
        $this->eventDispatcher           = $eventDispatcher;
        $this->commonSubscriptionUpdater = $commonSubscriptionUpdater;
        $this->subscriptionLimiter       = $subscriptionLimiter;
    }

    /**
     * @param Subscription     $subscription
     * @param ProcessResult    $processResponse
     * @param SessionInterface $session
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
     * @param Subscription     $subscription
     * @param ProcessResult    $response
     * @param SessionInterface $session
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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
                    //TODO: remove?
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
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function applySuccess(Subscription $subscription)
    {
        $subscription->setStatus(Subscription::IS_ACTIVE);

        $subscription->setError('');

        $renewDate = $this->renewDateCalculator->calculateRenewDate($subscription);
        $subscription->setRenewDate($renewDate);

        if (intval($subscription->getCredits()) === 0) {
            $User                 = $subscription->getUser();
            $existingSubscription = $this->subscriptionProvider->getExistingSubscriptionForUser($User);

            $newCredits = $this->creditsCalculator->calculateCredits($subscription, $subscription->getSubscriptionPack(), $existingSubscription);
            $subscription->setCredits($newCredits);
        }
    }

    protected function applyFailure(Subscription $subscription, string $errorName)
    {
        $subscription->setStatus(Subscription::IS_ERROR);
    }

}