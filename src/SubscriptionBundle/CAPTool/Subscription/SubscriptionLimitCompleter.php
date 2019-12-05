<?php


namespace SubscriptionBundle\CAPTool\Subscription;


use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SubscriptionLimitCompleter constructor.
     *
     * @param SubscriptionLimiter            $subscriptionLimiter
     * @param ProcessResultSuccessChecker    $resultSuccessChecker
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     * @param CampaignExtractor              $campaignExtractor
     * @param LoggerInterface                $logger
     */
    public function __construct(
        SubscriptionLimiter $subscriptionLimiter,
        ProcessResultSuccessChecker $resultSuccessChecker,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking,
        CampaignExtractor $campaignExtractor,
        LoggerInterface $logger
    )
    {
        $this->subscriptionLimiter            = $subscriptionLimiter;
        $this->resultSuccessChecker           = $resultSuccessChecker;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
        $this->campaignExtractor              = $campaignExtractor;
        $this->logger                         = $logger;
    }

    /**
     * @param ProcessResult $response
     * @param Subscription  $subscription
     */
    public function finishProcess(ProcessResult $response, Subscription $subscription): void
    {
        $user             = $subscription->getUser();
        $subscriptionPack = $subscription->getSubscriptionPack();
        $carrier          = $subscriptionPack->getCarrier();
        $campaign         = $this->campaignExtractor->getCampaignForSubscription($subscription);

        if ($this->resultSuccessChecker->isSuccessfulForCapTrack($response)) {

            $this->logger->debug('Cap section', [
                'campaign' => $campaign
                    ? $campaign->getCampaignToken()
                    : ''
            ]);
            $affiliateCapIsNeedToBeTracked = true;
            if (
                $this->zeroCreditSubscriptionChecking->isZeroCreditAvailable($carrier->getBillingCarrierId(), $campaign) &&
                $this->zeroCreditSubscriptionChecking->isZeroCreditSubscriptionPerformed($response)
            ) {
                $affiliateCapIsNeedToBeTracked = $subscriptionPack->getTrackAffiliateOnZeroCreditSub();
                $this->logger->debug('Zero credit check is triggered', [
                    'affiliateCapIsNeedToBeTracked' => $affiliateCapIsNeedToBeTracked
                ]);
            } else {
                $this->logger->debug('Zero credit is not required');
            }


            $this->subscriptionLimiter->finishSubscription(
                $user->getCarrier(),
                $subscription,
                $affiliateCapIsNeedToBeTracked,
                $campaign
            );

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