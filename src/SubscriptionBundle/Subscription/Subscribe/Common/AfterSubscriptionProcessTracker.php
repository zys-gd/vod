<?php


namespace SubscriptionBundle\Subscription\Subscribe\Common;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Common\AffiliateNotifier;
use SubscriptionBundle\Subscription\Subscribe\Common\SubscriptionEventTracker;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomPiwikTrackingRules;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;

class AfterSubscriptionProcessTracker
{
    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;
    /**
     * @var AffiliateNotifier
     */
    private $affiliateNotifier;

    /**
     * AfterSubscriptionProcessTracker constructor.
     *
     * @param SubscriptionEventTracker $subscriptionEventTracker
     */
    public function __construct(SubscriptionEventTracker $subscriptionEventTracker, AffiliateNotifier $affiliateNotifier)
    {
        $this->subscriptionEventTracker = $subscriptionEventTracker;
        $this->affiliateNotifier        = $affiliateNotifier;
    }

    /**
     * @param ProcessResult          $processResult
     * @param Subscription           $subscription
     * @param                        $subscriber
     * @param CampaignInterface|null $campaign
     */
    public function track(
        ProcessResult $processResult,
        Subscription $subscription,
        object $subscriber,
        CampaignInterface $campaign = null
    ): void
    {
        if ($subscriber instanceof HasCustomAffiliateTrackingRules) {
            $isAffTracked = $subscriber->isAffiliateTrackedForSub($processResult, $campaign);
        } else {
            $isAffTracked = ($processResult->isSuccessful() && $processResult->isFinal());
        }

        if ($isAffTracked) {
            $this->affiliateNotifier->notifyAffiliateAboutSubscription($subscription, $campaign);
        }

        if ($subscriber instanceof HasCustomPiwikTrackingRules) {
            $isPiwikTracked = $subscriber->isPiwikTrackedForSub($processResult);
        } else {
            $isPiwikTracked = ($processResult->isFailedOrSuccessful() && $processResult->isFinal());
        }

        if ($isPiwikTracked) {
            $this->subscriptionEventTracker->trackSubscribe($subscription, $processResult);
        }
    }
}