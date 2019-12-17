<?php

namespace SubscriptionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SubscriptionBundle\Entity\BlackList;

/**
 * Class BlackListRepository
 */
class BlackListRepository extends EntityRepository
{
    /**
     * @param \DateTimeInterface $dateTime
     * @return BlackList[]
     */
    public function findOutdatedEntries(\DateTimeInterface $dateTime)
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $query        = $queryBuilder
            ->where(':expirationDate > v.addedAt')
            ->setParameter('expirationDate', $dateTime)
            ->getQuery();

        return $query->getResult();
    }


}