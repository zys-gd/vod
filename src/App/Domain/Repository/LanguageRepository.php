<?php

namespace App\Domain\Repository;

/**
 * Class LanguageRepository
 */
class LanguageRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEnglishLanguageId()
    {
        return $this->createQueryBuilder('p2o')
            ->select('p2o.id')
            ->where("p2o.code = 'en'")
            ->getQuery()
            ->getSingleScalarResult();
    }
}