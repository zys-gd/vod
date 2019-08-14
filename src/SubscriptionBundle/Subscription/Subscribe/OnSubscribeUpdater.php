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
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Common\CommonSubscriptionUpdater;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator;
use SubscriptionBundle\Subscription\Subscribe\Service\CreditsCalculator;

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
    /**
     * @var \SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;


    /**
     * OnSubscribeUpdater constructor.
     *
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionExtractor        $subscriptionProvider
     * @param \SubscriptionBundle\Subscription\Subscribe\Service\CreditsCalculator $creditsCalculator
     * @param \SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator   $renewDateCalculator
     * @param  CommonSubscriptionUpdater                                           $commonSubscriptionUpdater
     * @param \SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker  $resultSuccessChecker
     */
    public function __construct(
        SubscriptionExtractor $subscriptionProvider,
        CreditsCalculator $creditsCalculator,
        RenewDateCalculator $renewDateCalculator,
        CommonSubscriptionUpdater $commonSubscriptionUpdater,
        ProcessResultSuccessChecker $resultSuccessChecker
    )
    {
        $this->subscriptionProvider      = $subscriptionProvider;
        $this->creditsCalculator         = $creditsCalculator;
        $this->renewDateCalculator       = $renewDateCalculator;
        $this->commonSubscriptionUpdater = $commonSubscriptionUpdater;
        $this->resultSuccessChecker      = $resultSuccessChecker;
    }

    /**
     * @param Subscription  $subscription
     * @param ProcessResult $processResponse
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
     * @param ProcessResult $result
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateSubscriptionByCallbackResponse(Subscription $subscription, ProcessResult $result)
    {
        $isSuccessful = $this->resultSuccessChecker->isSuccessful($result);

        if ($isSuccessful) {
            $this->applySuccess($subscription);
        }

        $this->commonSubscriptionUpdater->updateSubscriptionByCallbackResponse($subscription, $result);

        if (!$isSuccessful) {

            $subscription->setError($result->getError());

            switch ($result->getError()) {
                case 'not_enough_credit':
                    $subscription->setStatus(Subscription::IS_ON_HOLD);
                    //TODO: remove?
                    if ($subscription->getCredits() >= 2) {
                        $subscription->setCredits($subscription->getCredits() - 2);
                    }
                    break;
                default:
                    $this->applyFailure($subscription, $result->getError());
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