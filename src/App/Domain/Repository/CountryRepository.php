<?php

namespace App\Domain\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CountryRepository
 */
class CountryRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findEnabledCarriersCountryCodes(): array
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where($qb->expr()->eq('v.published', true))
            ->select('v.countryCode');

        return $qb->getQuery()->getResult('COLUMN_HYDRATOR');
    }
}