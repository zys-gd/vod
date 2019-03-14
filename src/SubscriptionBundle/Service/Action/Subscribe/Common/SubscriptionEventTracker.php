<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.03.19
 * Time: 18:52
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Common;


use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\SubscriptionStatisticSender;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionEventTracker
{
    /**
     * @var AffiliateSender
     */
    private $affiliateSender;
    /**
     * @var UserInfoMapper
     */
    private $infoMapper;
    /**
     * @var SubscriptionStatisticSender
     */
    private $subscriptionStatisticSender;
    /**
     * @var SessionInterface
     */
    private $session;


    /**
     * SubscriptionEventTracker constructor.
     * @param AffiliateSender             $affiliateSender
     * @param UserInfoMapper              $infoMapper
     * @param SubscriptionStatisticSender $sender
     */
    public function __construct(AffiliateSender $affiliateSender, UserInfoMapper $infoMapper, SubscriptionStatisticSender $sender, SessionInterface $session)
    {
        $this->affiliateSender             = $affiliateSender;
        $this->infoMapper                  = $infoMapper;
        $this->subscriptionStatisticSender = $sender;
        $this->session                     = $session;
    }

    public function trackAffiliate(Subscription $subscription): void
    {
        $this->affiliateSender->checkAffiliateEligibilityAndSendEvent(
            $subscription,
            $this->infoMapper->mapFromUser($subscription->getUser()),
            $subscription->getAffiliateToken(),
            AffiliateVisitSaver::extractCampaignToken($this->session)
        );
    }

    public function trackPiwikForSubscribe(Subscription $subscription, ProcessResult $response): void
    {
        $this->subscriptionStatisticSender->trackSubscribe(
            $subscription->getUser(),
            $subscription,
            $response
        );
    }

    public function trackPiwikForResubscribe(Subscription $subscription, ProcessResult $response): void
    {
        $this->subscriptionStatisticSender->trackResubscribe(
            $subscription->getUser(),
            $subscription,
            $response
        );
    }
}