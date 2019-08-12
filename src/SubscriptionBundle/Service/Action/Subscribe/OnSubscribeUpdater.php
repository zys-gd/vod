<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 11:36
 */

namespace SubscriptionBundle\Service\Action\Subscribe;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Common\CommonSubscriptionUpdater;
use SubscriptionBundle\Service\Action\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Service\CAPTool\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\CreditsCalculator;
use SubscriptionBundle\Service\RenewDateCalculator;
use SubscriptionBundle\Service\SubscriptionExtractor;

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
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;


    /**
     * OnSubscribeUpdater constructor.
     *
     * @param SubscriptionExtractor                           $subscriptionProvider
     * @param CreditsCalculator                               $creditsCalculator
     * @param \SubscriptionBundle\Service\RenewDateCalculator $renewDateCalculator
     * @param CommonSubscriptionUpdater                       $commonSubscriptionUpdater
     * @param ProcessResultSuccessChecker                     $resultSuccessChecker
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