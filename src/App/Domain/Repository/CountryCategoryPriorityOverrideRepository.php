<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Country;
use Doctrine\ORM\EntityRepository;

/**
 * Class CountryCategoryPriorityOverrideRepository
 */
class CountryCategoryPriorityOverrideRepository extends EntityRepository
{
    /**
     * @param int $carrierId
     *
     * @return array
     */
    public function findByBillingCarrierId(int $carrierId)
    {
        $queryBuilder = $this->createQueryBuilder('cpo');

        $query = $queryBuilder
            ->select('cpo')
            ->innerJoin(Carrier::class, 'ca', 'WITH', 'ca.billingCarrierId = :carrierId')
            ->innerJoin(Country::class, 'co', 'WITH', 'ca.countryCode = co.countryCode')
            ->where('cpo.country = co.uuid')
            ->setParameter('carrierId', $carrierId)
            ->getQuery();

        return $query->getResult();
    }
}