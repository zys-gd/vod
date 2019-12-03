<?php


namespace SubscriptionBundle\Subscription\Subscribe\Common;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
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
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;
    /**
     * @var AffiliateSender
     */
    private $affiliateSender;
    /**
     * @var UserInfoMapper
     */
    private $infoMapper;

    /**
     * AfterSubscriptionProcessTracker constructor.
     *
     * @param SubscriptionEventTracker       $subscriptionEventTracker
     * @param LoggerInterface                $logger
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     * @param AffiliateSender                $affiliateService
     * @param UserInfoMapper                 $infoMapper
     */
    public function __construct(
        SubscriptionEventTracker $subscriptionEventTracker,
        LoggerInterface $logger,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking,
        AffiliateSender $affiliateService,
        UserInfoMapper $infoMapper
    )
    {
        $this->subscriptionEventTracker       = $subscriptionEventTracker;
        $this->logger                         = $logger;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
        $this->affiliateSender                = $affiliateService;
        $this->infoMapper                     = $infoMapper;
    }

    /**
     * @param ProcessResult          $processResult
     * @param Subscription           $subscription
     * @param object                 $subscriber
     * @param CampaignInterface|null $campaign
     * @param bool                   $isResubscribe
     * @param array                  $campaignData
     */
    public function track(
        ProcessResult $processResult,
        Subscription $subscription,
        object $subscriber,
        CampaignInterface $campaign = null,
        bool $isResubscribe = false,
        array $campaignData = []
    ): void
    {
        $this->logger->debug('Start tracking after subscription', [
            $processResult
        ]);

        if ($campaign) {
            $isAffTracked = $this->resolveAffTrackedCondition($processResult, $subscriber, $campaign, $subscription->getSubscriptionPack());

            if ($isAffTracked) {
                $this->affiliateSender->checkAffiliateEligibilityAndSendEvent(
                    $subscription,
                    $this->infoMapper->mapFromUser($subscription->getUser()),
                    $subscription->getAffiliateToken(),
                    $campaign->getCampaignToken(),
                    $campaignData
                );
            }
        }
        else {
            $isAffTracked = false;
        }

        if ($subscriber instanceof HasCustomPiwikTrackingRules) {
            $isPiwikTracked = $subscriber->isPiwikTrackedForSub($processResult);
        }
        else {
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
            }
            else {
                $this->subscriptionEventTracker->trackSubscribe($subscription, $processResult);
            }
        }
    }

    /**
     * @param ProcessResult     $processResult
     * @param object            $subscriber
     * @param CampaignInterface $campaign
     * @param SubscriptionPack  $subscriptionPack
     *
     * @return bool
     */
    private function resolveAffTrackedCondition(
        ProcessResult $processResult,
        object $subscriber,
        CampaignInterface $campaign,
        SubscriptionPack $subscriptionPack
    ): bool
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