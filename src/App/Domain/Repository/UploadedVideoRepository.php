<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Subcategory;

/**
 * UploadedVideoRepository
 */
class UploadedVideoRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     *
     * @throws \Exception
     */
    public function findNotExpiredWithCategories(): array
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $q = $queryBuilder
            ->leftJoin('v.subcategory', 'subcategory')
            ->leftJoin('subcategory.parent', 'category')
            ->where($queryBuilder->expr()->orX('v.expiredDate > :currentDateTime', 'v.expiredDate IS NULL'))
            ->orderBy('v.createdDate', 'DESC')
            ->setParameter('currentDateTime', new \DateTime())
            ->addSelect('subcategory', 'category');

        return $q->getQuery()->getResult();
    }

    /**
     * @param Subcategory[] $subcategories
     *
     * @return array
     *
     * @throws \Exception
     */
    public function findNotExpiredBySubcategories(array $subcategories): array
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $query = $queryBuilder
            ->where($queryBuilder->expr()->orX('v.expiredDate > :currentDateTime', 'v.expiredDate IS NULL'))
            ->andWhere('v.subcategory = :subcategory')
            ->orderBy('v.createdDate', 'DESC')
            ->setParameters([
                'subcategory' => $subcategories,
                'currentDateTime' => new \DateTime()
            ])
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function findExpiredVideo(): array
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $query = $queryBuilder
            ->where(':currentDateTime > v.expiredDate')
            ->setParameter('currentDateTime', new \DateTime())
            ->getQuery();

        return $query->getResult();
    }
}