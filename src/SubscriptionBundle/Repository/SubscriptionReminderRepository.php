<?php

namespace SubscriptionBundle\Repository;

use Doctrine\ORM\EntityRepository;

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
    public function updateSentDateBySubscriptions(array $subscriptions)
    {
        $queryBuilder = $this->createQueryBuilder('sr');

        $query = $queryBuilder
            ->update()
            ->set('sr.lastReminderSent', ':currentDate')
            ->where(
                $queryBuilder
                    ->expr()
                    ->in('sr.subscription', $subscriptions)
            )
            ->setParameter('currentDate', new \DateTime())
            ->getQuery();

        $query->execute();
    }
}