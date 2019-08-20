<?php


namespace SubscriptionBundle\Service\Action\Subscribe;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Common\SubscriptionEventTracker;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomPiwikTrackingRules;

class AfterSubscriptionProcessTracker
{
    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;

    /**
     * AfterSubscriptionProcessTracker constructor.
     *
     * @param SubscriptionEventTracker $subscriptionEventTracker
     */
    public function __construct(SubscriptionEventTracker $subscriptionEventTracker)
    {
        $this->subscriptionEventTracker = $subscriptionEventTracker;
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
        $subscriber,
        CampaignInterface $campaign = null
    ): void
    {
        if ($subscriber instanceof HasCustomAffiliateTrackingRules) {
            $isAffTracked = $subscriber->isAffiliateTrackedForSub($processResult, $campaign);
        }
        else {
            $isAffTracked = ($processResult->isSuccessful() && $processResult->isFinal());
        }

        if ($isAffTracked) {
            $this->subscriptionEventTracker->trackAffiliate($subscription);
        }

        if ($subscriber instanceof HasCustomPiwikTrackingRules) {
            $isPiwikTracked = $subscriber->isPiwikTrackedForSub($processResult);
        }
        else {
            $isPiwikTracked = ($processResult->isFailedOrSuccessful() && $processResult->isFinal());
        }

        if ($isPiwikTracked) {
            $this->subscriptionEventTracker->trackPiwikForSubscribe($subscription, $processResult);
        }
    }
}