<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:36
 */

namespace SubscriptionBundle\Service\Action\Subscribe;


use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\SubscriptionLimiter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Common\CommonSubscriptionUpdater;
use SubscriptionBundle\Service\CreditsCalculator;
use SubscriptionBundle\Service\RenewDateCalculator;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OnSubscribeUpdater
{
    /**
     * @var SubscriptionExtractor
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
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;


    /**
     * OnSubscribeUpdater constructor.
     *
     * @param SubscriptionExtractor                           $subscriptionProvider
     * @param CreditsCalculator                               $creditsCalculator
     * @param \SubscriptionBundle\Service\RenewDateCalculator $renewDateCalculator
     * @param EventDispatcherInterface                        $eventDispatcher
     * @param CommonSubscriptionUpdater                       $commonSubscriptionUpdater
     * @param SubscriptionLimiter                             $subscriptionLimiter
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
    public function updateSubscriptionByResponse(Subscription $subscription, ProcessResult $processResponse, SessionInterface $session)
    {
        $this->updateSubscriptionByCallbackResponse($subscription, $processResponse, $session);

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
    public function updateSubscriptionByCallbackResponse(Subscription $subscription, ProcessResult $response, SessionInterface $session)
    {
        $carrierLimiterData = new CarrierLimiterData($subscription->getUser()->getCarrier());
        $this->subscriptionLimiter->setLimiterData($session, $carrierLimiterData);

        if ($response->isSuccessful()) {
            $this->applySuccess($subscription);

            $this->subscriptionLimiter->finishLimitingProcess($carrierLimiterData);
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
                    $this->subscriptionLimiter->cancelLimitingProcess($carrierLimiterData);
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