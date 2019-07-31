<?php

namespace SubscriptionBundle\Service;


use ExtrasBundle\Utils\UuidGenerator;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;

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

    public function createAndSave(User $user, SubscriptionPack $subscriptionPack, $affiliateToken = null): Subscription
    {
        $subscription = $this->create($user, $subscriptionPack, $affiliateToken);
        $this->entitySaveHelper->persistAndSave($subscription);

        return $subscription;
    }

    public function create(User $user, SubscriptionPack $subscriptionPack, $affiliateToken = null, int $status = Subscription::IS_ACTIVE): Subscription
    {
        $uuid = UuidGenerator::generate();
        $subscription = new Subscription($uuid);
        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setUser($user);
        $subscription->setAffiliateToken(json_encode($affiliateToken));
        $subscription->setStatus($status);

        return $subscription;
    }




}