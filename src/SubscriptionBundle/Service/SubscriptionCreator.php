<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 15:49
 */

namespace SubscriptionBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPlanInterface;
use UserBundle\Entity\BillableUser;

class SubscriptionCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    /**
     * SubscriptionCreator constructor.
     * @param EntitySaveHelper $entitySaveHelper
     */
    public function __construct(EntitySaveHelper $entitySaveHelper)
    {
        $this->entitySaveHelper = $entitySaveHelper;
    }

    public function createAndSave(BillableUser $billableUser, SubscriptionPlanInterface $plan, $affiliateToken = null): Subscription
    {
        $subscription = $this->create($billableUser, $plan, $affiliateToken);
        $this->entitySaveHelper->persistAndSave($subscription);

        return $subscription;
    }

    public function create(BillableUser $billableUser, SubscriptionPlanInterface $plan, $affiliateToken = null, int $status = Subscription::IS_ACTIVE): Subscription
    {
        $subscription = new Subscription();
        $subscription->setSubscriptionPack($plan);
        $subscription->setOwner($billableUser);
        $subscription->setAffiliateToken($affiliateToken);
        $subscription->setStatus($status);

        return $subscription;
    }




}