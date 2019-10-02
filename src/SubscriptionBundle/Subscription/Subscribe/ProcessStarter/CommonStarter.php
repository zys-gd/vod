<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.09.19
 * Time: 14:00
 */

namespace SubscriptionBundle\Subscription\Subscribe\ProcessStarter;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Common\SendNotificationChecker;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\Common\SubscribePerformer;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\Common\SendNotificationPerformer;

class CommonStarter implements SubscribeProcessStarterInterface
{

    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;
    /**
     * @var SubscribePerformer
     */
    private $subscribePerformer;
    /**
     * @var SendNotificationPerformer
     */
    private $sendNotificationPerformer;
    /**
     * @var SendNotificationChecker
     */
    private $sendNotificationChecker;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    /**
     * CommonStarter constructor.
     * @param ProcessResultSuccessChecker $resultSuccessChecker
     * @param SubscribePerformer          $subscribePerformer
     * @param SendNotificationPerformer   $subscribePromotionalPerformer
     * @param SendNotificationChecker     $sendNotificationChecker
     * @param CampaignExtractor           $campaignExtractor
     */
    public function __construct(
        ProcessResultSuccessChecker $resultSuccessChecker,
        SubscribePerformer $subscribePerformer,
        SendNotificationPerformer $subscribePromotionalPerformer,
        SendNotificationChecker $sendNotificationChecker,
        CampaignExtractor $campaignExtractor
    )
    {
        $this->resultSuccessChecker      = $resultSuccessChecker;
        $this->subscribePerformer        = $subscribePerformer;
        $this->sendNotificationPerformer = $subscribePromotionalPerformer;
        $this->sendNotificationChecker   = $sendNotificationChecker;
        $this->campaignExtractor         = $campaignExtractor;
    }

    public function isSupports(CarrierInterface $carrier): bool
    {
        return true;
    }

    public function start(Subscription $subscription, SubscriptionPack $pack, array $additionalData = []): ProcessResult
    {
        $campaign                            = $this->campaignExtractor->getCampaignForSubscription($subscription);
        $isFreeTrialSubscriptionFromCampaign = $campaign && $campaign->isFreeTrialSubscription();

        if (!$this->sendNotificationChecker->isNotificationShouldBeSent($subscription)) {
            return $this->subscribePerformer->doSubscribe($subscription, $additionalData);
        }

        if ($pack->isFirstSubscriptionPeriodIsFree() || $isFreeTrialSubscriptionFromCampaign) {
            // Phuck dat.
            $additionalData = array_merge($additionalData,['isTrial' => true]);
        }

        $response = $this->subscribePerformer->doSubscribe($subscription, $additionalData);
        if ($this->resultSuccessChecker->isSuccessful($response)) {
            $this->sendNotificationPerformer->doSentNotification($subscription);
        }

        return $response;
    }

    public function startResubscribe(Subscription $subscription, SubscriptionPack $plan, array $additionalData): ProcessResult
    {
        if ($this->sendNotificationChecker->isNotificationShouldBeSent($subscription)) {
            $response = $this->sendNotificationPerformer->doSentNotification($subscription);
            $this->subscribePerformer->doSubscribe($subscription, $additionalData);
        } else {
            $response = $this->subscribePerformer->doSubscribe($subscription, $additionalData);
        }

        return $response;
    }
}