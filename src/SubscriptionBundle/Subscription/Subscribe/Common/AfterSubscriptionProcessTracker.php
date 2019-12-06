<?php


namespace SubscriptionBundle\Subscription\Subscribe\Common;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
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
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;

    /**
     * AfterSubscriptionProcessTracker constructor.
     *
     * @param SubscriptionEventTracker       $subscriptionEventTracker
     * @param AffiliateNotifier              $affiliateNotifier
     * @param LoggerInterface                $logger
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     */
    public function __construct(
        SubscriptionEventTracker $subscriptionEventTracker,
        AffiliateNotifier $affiliateNotifier,
        LoggerInterface $logger,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
    )
    {
        $this->subscriptionEventTracker       = $subscriptionEventTracker;
        $this->affiliateNotifier              = $affiliateNotifier;
        $this->logger                         = $logger;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
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
            $isAffTracked = $this->resolveAffTrackedCondition($processResult, $subscriber, $campaign, $subscription->getSubscriptionPack());

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

    /**
     * @param ProcessResult     $processResult
     * @param object            $subscriber
     * @param CampaignInterface $campaign
     * @param SubscriptionPack  $subscriptionPack
     * @return bool
     */
    private function resolveAffTrackedCondition(ProcessResult $processResult, object $subscriber, CampaignInterface $campaign, SubscriptionPack $subscriptionPack): bool
    {
        if ($subscriber instanceof HasCustomAffiliateTrackingRules) {
            return $subscriber->isAffiliateTrackedForSub($processResult, $campaign);
        }

        $carrier = $subscriptionPack->getCarrier();
        if (
            $this->zeroCreditSubscriptionChecking->isZeroCreditAvailable($carrier->getBillingCarrierId(), $campaign) &&
            $this->zeroCreditSubscriptionChecking->isZeroCreditSubscriptionPerformed($processResult)
        ) {
            return $processResult->isSuccessful() && $subscriptionPack->getTrackAffiliateOnZeroCreditSub();
        }

        return $processResult->isSuccessful();
    }
}