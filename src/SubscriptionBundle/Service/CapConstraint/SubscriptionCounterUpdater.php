<?php

namespace SubscriptionBundle\Service\CapConstraint;

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
     * @var ConstraintCounterRedis
     */
    protected $constraintCounterRedis;

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
     * @param ConstraintCounterRedis $constraintCounterRedis
     * @param EntityManagerInterface $entityManager
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintCounterRedis $constraintCounterRedis,
        EntityManagerInterface $entityManager,
        CampaignRepositoryInterface $campaignRepository
    ) {
        $this->notificationSender = $notificationSender;
        $this->constraintCounterRedis = $constraintCounterRedis;
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

        if ($carrier->getNumberOfAllowedSubscriptionsByConstraint()) {
            $this->constraintCounterRedis->updateCounter($carrier->getBillingCarrierId());
        }

        $affiliateToken = $subscription->getAffiliateToken();

        if (!empty($affiliateToken['cid'])) {
            $campaign = $this->campaignRepository->findOneByCampaignToken($affiliateToken['cid']);
            $affiliate = $campaign->getAffiliate();

            $subscriptionConstraint = $affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE, $carrier);

            if ($subscriptionConstraint) {
                $this->constraintCounterRedis->updateCounter($subscriptionConstraint->getUuid());
            }
        }
    }
}