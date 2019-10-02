<?php

namespace IdentificationBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TestUserRepository extends EntityRepository
{

    public function findActiveUsers(): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->addSelect('carrier');

        $qb->join('a.carrier', 'carrier');

        $qb->where('carrier.published = 1');

        $qb->orderBy('a.lastTimeUsedAt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}