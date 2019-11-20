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
     * @return array
     *
     * @throws \Exception
     */
    public function findIdsOfNotExpiredVideos(): array
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $q            = $queryBuilder
            ->select('v.uuid as videoId')
            ->leftJoin('v.subcategory', 'subcategory')
            ->leftJoin('subcategory.parent', 'category')
            ->where($queryBuilder->expr()->orX('v.expiredDate > :currentDateTime', 'v.expiredDate IS NULL'))
            ->andWhere('v.status = :status')
            ->andWhere('v.pause = 0')
            ->orderBy('v.createdDate', 'DESC')
            ->addSelect('category.title as categoryName')
            ->setParameter('currentDateTime', new \DateTime())
            ->setParameter('status', UploadedVideo::STATUS_READY);

        return $q->getQuery()->getArrayResult();
    }

    /**
     * @param Subcategory[] $subcategories
     *
     * @param int           $offset
     * @param int           $count
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
                'subcategory'     => $subcategories,
                'currentDateTime' => new \DateTime(),
                'status'          => UploadedVideo::STATUS_READY
            ]);

        $queryBuilder->setMaxResults($count);
        $queryBuilder->setFirstResult($offset);

        $paginator = new Paginator($queryBuilder);
        $total     = $paginator->count();

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
        $query        = $queryBuilder
            ->where(':currentDateTime > v.expiredDate')
            ->setParameter('currentDateTime', new \DateTime())
            ->getQuery();

        return $query->getResult();
    }

    public function findWithCategories(array $ids = []): array
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $q            = $queryBuilder
            ->leftJoin('v.subcategory', 'subcategory')
            ->leftJoin('subcategory.parent', 'category')
            ->orderBy('v.createdDate', 'DESC')
            ->addSelect('category', 'subcategory');

        if ($ids) {
            $q->andWhere('v.uuid in (:ids)');
            $q->setParameter(':ids', $ids);
        }

        return $q->getQuery()->getResult();
    }

    public function findOutdatedVideo(\DateTimeInterface $dateTime)
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $query        = $queryBuilder
            ->where(':expirationDate > v.createdDate')
            ->setParameter('expirationDate', $dateTime)
            ->getQuery();

        return $query->getResult();
    }
}