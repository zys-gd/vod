<?php

namespace App\Domain\Repository;

use App\Domain\DTO\BatchOfNotExpiredVideos;
use App\Domain\Entity\Subcategory;
use App\Domain\Entity\UploadedVideo;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * UploadedVideoRepository
 */
class UploadedVideoRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param int $offset
     * @param int $count
     *
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
            ->andWhere('v.status = :status')
            ->andWhere('v.pause = 0')
            ->orderBy('v.createdDate', 'DESC')
            ->setParameter('currentDateTime', new \DateTime())
            ->setParameter('status', UploadedVideo::STATUS_READY)
            ->addSelect('subcategory', 'category');

        return $q->getQuery()->getResult();
    }

    /**
     * @param Subcategory[] $subcategories
     *
     * @param int $offset
     * @param int $count
     * @return BatchOfNotExpiredVideos
     *
     * @throws \Exception
     */
    public function findNotExpiredBySubcategories(array $subcategories, int $offset = 0, int $count = 20): BatchOfNotExpiredVideos
    {
        $queryBuilder = $this->createQueryBuilder('v');

        $queryBuilder
            ->where($queryBuilder->expr()->orX('v.expiredDate > :currentDateTime', 'v.expiredDate IS NULL'))
            ->andWhere('v.subcategory = :subcategory')
            ->andWhere('v.status = :status')
            ->andWhere('v.pause = 0')
            ->orderBy('v.createdDate', 'DESC')
            ->setParameters([
                'subcategory' => $subcategories,
                'currentDateTime' => new \DateTime(),
                'status' => UploadedVideo::STATUS_READY
            ]);

        $queryBuilder->setMaxResults($count);
        $queryBuilder->setFirstResult($offset);

        $paginator = new Paginator($queryBuilder);
        $total = $paginator->count();

        return new BatchOfNotExpiredVideos(
            $queryBuilder->getQuery()->getResult() ?? [],
            $total <= ($count + $offset)
        );
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