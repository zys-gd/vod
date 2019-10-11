<?php


namespace SubscriptionBundle\Subscription\Subscribe\Common;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomPiwikTrackingRules;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AfterSubscriptionProcessTracker constructor.
     *
     * @param SubscriptionEventTracker                                            $subscriptionEventTracker
     * @param \SubscriptionBundle\Subscription\Subscribe\Common\AffiliateNotifier $affiliateNotifier
     * @param LoggerInterface                                                     $logger
     */
    public function __construct(
        SubscriptionEventTracker $subscriptionEventTracker,
        AffiliateNotifier $affiliateNotifier,
        LoggerInterface $logger
    )
    {
        $this->subscriptionEventTracker = $subscriptionEventTracker;
        $this->affiliateNotifier        = $affiliateNotifier;
        $this->logger                   = $logger;
    }

    /**
     * @param ProcessResult          $processResult
     * @param Subscription           $subscription
     * @param object                 $subscriber
     * @param CampaignInterface|null $campaign
     * @param bool                   $isResubscribe
     */
    public function track(
        ProcessResult $processResult,
        Subscription $subscription,
        object $subscriber,
        CampaignInterface $campaign = null,
        bool $isResubscribe = false
    ): void
    {
        $this->logger->debug('Start tracking after subscription', [
            $processResult
        ]);


        if ($campaign) {
            if ($subscriber instanceof HasCustomAffiliateTrackingRules) {
                $isAffTracked = $subscriber->isAffiliateTrackedForSub($processResult, $campaign);
            } else {
                $isAffTracked = ($processResult->isSuccessful());
            }

            if ($isAffTracked) {
                $this->affiliateNotifier->notifyAffiliateAboutSubscription($subscription, $campaign);
            }
        } else {
            $isAffTracked = false;
        }

        if ($subscriber instanceof HasCustomPiwikTrackingRules) {
            $isPiwikTracked = $subscriber->isPiwikTrackedForSub($processResult);
        } else {
            $isPiwikTracked = ($processResult->isFailedOrSuccessful());
        }

        $this->logger->debug('Tracking after subscription state', [
            'isAffTracked'   => $isAffTracked,
            'isPiwikTracked' => $isPiwikTracked,
            $campaign
        ]);

        if ($isPiwikTracked) {

            // Not best idea definitely. Not even close. However, im not sure if duplicate code is an better option.
            //
            // I'll prefer siding with the evil we know over the evil we don't.
            if ($isResubscribe) {
                $this->subscriptionEventTracker->trackResubscribe($subscription, $processResult);
            } else {
                $this->subscriptionEventTracker->trackSubscribe($subscription, $processResult);
            }
        }
    }
}