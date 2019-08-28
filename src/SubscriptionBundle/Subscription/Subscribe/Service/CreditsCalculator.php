<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.05.18
 * Time: 11:55
 */

namespace SubscriptionBundle\Subscription\Subscribe\Service;


use Carbon\Carbon;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;

class CreditsCalculator
{
    private $renewDateCalculator;

    /**
     * CreditsCalculator constructor.
     * @param $renewDateCalculator
     */
    public function __construct(\SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator $renewDateCalculator)
    {
        $this->renewDateCalculator = $renewDateCalculator;
    }


    /**
     * @param Subscription      $subscription
     * @param SubscriptionPack  $plan
     * @param Subscription|null $existingSubscription
     * @return int
     */
    public function calculateCredits(
        Subscription $subscription,
        SubscriptionPack $plan,
        Subscription $existingSubscription = null
    )
    {
        if ($plan->isUnlimited()) {
            return PHP_INT_MAX - 100;
        } else {
            return $this->calculateForLimitedPlan($subscription, $plan, $existingSubscription);
        }

    }

    private function calculateForLimitedPlan(Subscription $subscription, SubscriptionPack $subscriptionPack, Subscription $existingSubscription = null)
    {
        if ($subscription->getPreviousStage() == Subscription::ACTION_SUBSCRIBE) {
            return $subscriptionPack->getCredits();
        }
        if ($subscription->getPreviousStage() == Subscription::ACTION_RENEW) {
            return $subscriptionPack->getCredits();
        }


        if ($existingSubscription) {
            $currentCredits = $this->calculateBasedOnExistinSubscription($subscription, $existingSubscription);
        } else {
            $currentCredits = $subscription->getCredits();
        }

        if (
            $subscription->getSubscriptionPack()->isAllowBonusCredit() &&
            $subscription->getCurrentStage() == Subscription::ACTION_SUBSCRIBE
        ) {
            $currentCredits += $subscription->getSubscriptionPack()->getBonusCredit();
        }

        $planCredits = $subscriptionPack->getCredits();
        $newCredits  = $currentCredits + $planCredits;

        return $newCredits;
    }

    /**
     * @param Subscription $subscription
     * @param              $existingSubscription
     * @return int
     */
    private function calculateBasedOnExistinSubscription(Subscription $subscription, Subscription $existingSubscription): int
    {
        $currentCredits = $subscription->getCredits();
        if ($existingSubscription->getSubscriptionPack()->hasUnlimitedGracePeriod()) {
            $currentCredits = $existingSubscription->getCredits();
        } else {
            $creditExpirationTime = $this->calculateCreditExpirationTime($subscription, $existingSubscription);

            $gracePeriod = $existingSubscription->getSubscriptionPack()->getGracePeriod();
            if ($gracePeriod > 0) {
                $creditExpirationTime->addDays($gracePeriod);
            }

            if ($creditExpirationTime->greaterThan(Carbon::now())) {
                $currentCredits = $existingSubscription->getCredits();
            }
        }
        if ($subscription->getSubscriptionPack()->isAllowBonusCredit() &&
            $subscription->getSubscriptionPack()->isAllowBonusCreditMultiple()) {
            $currentCredits += $subscription->getSubscriptionPack()->getBonusCredit();
        }
        return $currentCredits;
    }

    /**
     * @param Subscription $subscription
     * @param Subscription $existingSubscription
     * @return Carbon
     */
    private function calculateCreditExpirationTime(Subscription $subscription, Subscription $existingSubscription): Carbon
    {
        $lastRenewDate = $existingSubscription->getRenewDate();
        if (!$lastRenewDate) {
            $creditExpirationTime = $this->renewDateCalculator->calculateRenewDate($subscription);
        } else {
            $creditExpirationTime = Carbon::instance($lastRenewDate);
        }
        return $creditExpirationTime;
    }
}