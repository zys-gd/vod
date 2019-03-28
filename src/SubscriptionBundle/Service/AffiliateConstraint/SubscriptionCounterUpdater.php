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
     * @var ConstraintByAffiliateRedis
     */
    protected $constraintByAffiliateRedis;

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
     * @param ConstraintByAffiliateRedis $constraintByAffiliateRedis
     * @param EntityManagerInterface $entityManager
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintByAffiliateRedis $constraintByAffiliateRedis,
        EntityManagerInterface $entityManager,
        CampaignRepositoryInterface $campaignRepository
    ) {
        $this->notificationSender = $notificationSender;
        $this->constraintByAffiliateRedis = $constraintByAffiliateRedis;
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
            $this->constraintByAffiliateRedis->updateCounter($subscriptionConstraint);
        }
    }
}