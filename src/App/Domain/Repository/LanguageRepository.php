<?php

namespace App\Domain\Repository;

use Doctrine\ORM\AbstractQuery;
use IdentificationBundle\Entity\LanguageInterface;
use IdentificationBundle\Repository\LanguageRepositoryInterface;

/**
 * Class LanguageRepository
 */
class LanguageRepository extends \Doctrine\ORM\EntityRepository implements LanguageRepositoryInterface
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

    public function findByCode(string $code): ?LanguageInterface
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function getOrderedLanguagesByCodes(array $langCodes)
    {
        $query = $this->createQueryBuilder('p2o')
            ->select('p2o.uuid')
            ->where('p2o.code IN(:langCodes)')
            ->setParameter('langCodes', $langCodes)
            ->getQuery();

        return $query->getResult("COLUMN_HYDRATOR");
    }
}