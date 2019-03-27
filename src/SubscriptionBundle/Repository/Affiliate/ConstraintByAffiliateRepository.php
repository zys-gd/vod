<?php

namespace SubscriptionBundle\Repository\Affiliate;

use Doctrine\ORM\EntityRepository;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;

/**
 * Class ConstraintByAffiliateRepository
 */
class ConstraintByAffiliateRepository extends EntityRepository
{
    /**
     * @param AffiliateInterface $affiliate
     * @param CarrierInterface $carrier
     * @param string $capType
     *
     * @return bool
     */
    public function hasIdenticalConstraints(
        AffiliateInterface $affiliate,
        CarrierInterface $carrier,
        string $capType
    ): bool
    {
        $queryBuilder = $this->createQueryBuilder('cba');

        $query = $queryBuilder
            ->select('cba.uuid')
            ->where(
                $queryBuilder
                    ->expr()
                    ->andX('cba.affiliate = :affiliate', 'cba.carrier = :carrier', 'cba.capType = :capType')
            )
            ->setParameters([
                'affiliate' => $affiliate,
                'carrier' => $carrier,
                'capType' => $capType
            ])
            ->getQuery();

        return count($query->getResult()) > 0;
    }
}