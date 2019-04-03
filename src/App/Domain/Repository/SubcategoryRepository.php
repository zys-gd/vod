<?php

namespace App\Domain\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 */
class SubcategoryRepository extends EntityRepository
{
    public function getIdenticalSubcategoryUuid(string $title)
    {
        $queryBuilder = $this->createQueryBuilder('sc');

        $query = $queryBuilder
            ->select('sc.uuid')
            ->where('sc.title = :title')
            ->setParameter('title', $title)
            ->getQuery();

        $result = array_column($query->getScalarResult(), 'uuid');

        return count($result) > 0 ? $result[0] : null;
    }
}
