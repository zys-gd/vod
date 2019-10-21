<?php


namespace SubscriptionBundle\CAPTool\Subscription;


use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking;

class SubscriptionLimitCompleter
{
    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;
    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    /**
     * SubscriptionLimitCompleter constructor.
     *
     * @param SubscriptionLimiter            $subscriptionLimiter
     * @param ProcessResultSuccessChecker    $resultSuccessChecker
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     * @param CampaignExtractor              $campaignExtractor
     */
    public function __construct(
        SubscriptionLimiter $subscriptionLimiter,
        ProcessResultSuccessChecker $resultSuccessChecker,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking,
        CampaignExtractor $campaignExtractor
    )
    {
        $this->subscriptionLimiter            = $subscriptionLimiter;
        $this->resultSuccessChecker           = $resultSuccessChecker;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
        $this->campaignExtractor              = $campaignExtractor;
    }

    /**
     * @param ProcessResult $response
     * @param Subscription  $subscription
     */
    public function finishProcess(ProcessResult $response, Subscription $subscription): void
    {
        $user     = $subscription->getUser();
        $carrier  = $subscription->getSubscriptionPack()->getCarrier();
        $campaign = $this->campaignExtractor->getCampaignForSubscription($subscription);

        if ($this->resultSuccessChecker->isSuccessful($response)) {
            $isCapNeedToBeStored = true;
            if (
                $this->zeroCreditSubscriptionChecking->isZeroCreditAvailable($carrier->getBillingCarrierId(), $campaign) &&
                $this->zeroCreditSubscriptionChecking->isZeroCreditSubscriptionPerformed($response)
            ) {
                $isCapNeedToBeStored = false;
            }

            if ($isCapNeedToBeStored) {
                $this->subscriptionLimiter->finishSubscription($user->getCarrier(), $subscription, $campaign);
            } else {
                $this->subscriptionLimiter->releasePendingSlot($user->getCarrier());
            }
            return;

        } else {
            switch ($response->getError()) {
                case 'not_enough_credit':
                    //TODO: remove?
                    break;
                default:
                    $this->subscriptionLimiter->releasePendingSlot($user->getCarrier());
            }
        }
    }
}