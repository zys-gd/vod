<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Country;
use Doctrine\ORM\AbstractQuery;
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
        $qb->innerJoin(Carrier::class, 'c','WITH', 'c.countryCode = v.countryCode AND c.published = true')
            ->select('v')
            ->groupBy('c.countryCode');

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
    }
}