<?php

namespace App\Domain\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class MainCategoryRepository
 */
class MainCategoryRepository extends EntityRepository
{

    public function findWithSubcategories(): array
    {
        $q = $this->createQueryBuilder('c')
            ->addSelect('subcategories','c')
            ->join('c.subcategories', 'subcategories');

        return $q->getQuery()->getResult();

    }
}