<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\MainCategory;
use CommonDataBundle\Entity\Country;
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
    public function findByBillingCarrierId(int $carrierId): array
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

    /**
     * @param MainCategory $mainCategory
     * @param Country $country
     * @param int $menuPriority
     *
     * @return bool
     */
    public function checkForIdenticalOverrides(MainCategory $mainCategory, Country $country, int $menuPriority): bool
    {
        $queryBuilder = $this->createQueryBuilder('cpo');

        $query = $queryBuilder
            ->select('cpo.uuid')
            ->where($queryBuilder->expr()->andX('cpo.mainCategory = :mainCategory', 'cpo.country = :country'))
            ->orWhere($queryBuilder->expr()->andX('cpo.menuPriority = :menuPriority', 'cpo.country = :country'))
            ->setParameters([
                'mainCategory' => $mainCategory,
                'country' => $country,
                'menuPriority' => $menuPriority
            ])
            ->getQuery();

        return count($query->getResult()) > 0;
    }
}