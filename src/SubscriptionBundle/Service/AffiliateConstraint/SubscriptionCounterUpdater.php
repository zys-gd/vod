<?php

namespace SubscriptionBundle\Service\AffiliateConstraint;

use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;
use SubscriptionBundle\Service\Notification\Email\CAPNotificationSender;

/**
 * Class SubscriptionCounterUpdater
 */
class SubscriptionCounterUpdater
{
    /**
     * @var CAPNotificationSender
     */
    protected $notificationSender;

    /**
     * @var ConstraintByAffiliateCache
     */
    protected $cache;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * AbstractConstraintByAffiliateService constructor
     *
     * @param CAPNotificationSender $notificationSender
     * @param ConstraintByAffiliateCache $cache
     * @param EntityManagerInterface $entityManager
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintByAffiliateCache $cache,
        EntityManagerInterface $entityManager,
        CampaignRepositoryInterface $campaignRepository
    ) {
        $this->notificationSender = $notificationSender;
        $this->cache = $cache;
        $this->entityManager = $entityManager;
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @param Subscription $subscription
     */
    public function updateSubscriptionCounter(Subscription $subscription): void
    {
        $user = $subscription->getUser();
        $carrier = $user->getCarrier();

        $affiliateToken = $subscription->getAffiliateToken();

        $campaign = $this->campaignRepository->findOneByCampaignToken($affiliateToken['cid']);
        $affiliate = $campaign->getAffiliate();

        $subscriptionConstraint = $affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE, $carrier);

        if ($subscriptionConstraint) {
            $this->cache->updateCounter($subscriptionConstraint);
        }
    }
}