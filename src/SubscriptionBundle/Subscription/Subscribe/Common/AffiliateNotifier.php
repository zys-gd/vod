<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.08.19
 * Time: 16:27
 */

namespace SubscriptionBundle\Subscription\Subscribe\Common;


use App\Domain\Entity\Campaign;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AffiliateNotifier
{
    /**
     * @var AffiliateSender
     */
    private $sender;
    /**
     * @var UserInfoMapper
     */
    private $infoMapper;


    /**
     * AffiliateNotifier constructor.
     * @param AffiliateSender $sender
     * @param UserInfoMapper  $infoMapper
     */
    public function __construct(AffiliateSender $sender, UserInfoMapper $infoMapper)
    {
        $this->sender     = $sender;
        $this->infoMapper = $infoMapper;
    }

    public function notifyAffiliateAboutSubscription(Subscription $subscription, CampaignInterface $campaign): void
    {
        $this->sender->checkAffiliateEligibilityAndSendEvent(
            $subscription,
            $this->infoMapper->mapFromUser($subscription->getUser()),
            $campaign->getCampaignToken(),
            $subscription->getAffiliateToken() ?? []
        );
    }

}