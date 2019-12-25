<?php

namespace SubscriptionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SubscriptionBundle\Entity\Subscription;

/**
 * Class SubscriptionReminderRepository
 */
class SubscriptionReminderRepository extends EntityRepository
{
    /**
     * @param array $subscriptions
     *
     * @throws \Exception
     */
    public function deleteBySubscriptions(array $subscriptions)
    {
        $queryBuilder = $this->createQueryBuilder('sr');

        $query = $queryBuilder
            ->delete()
            ->where(
                $queryBuilder
                    ->expr()
                    ->in('sr.subscription', ':subscriptions')
            )
            ->setParameter('subscriptions', array_map(function($subscription) {
                return $subscription->getUuid();
            }, $subscriptions))
            ->getQuery();

        $query->execute();
    }
}