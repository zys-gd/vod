<?php

namespace SubscriptionBundle\Repository\Affiliate;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

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
     * @return string|null
     */
    public function getIdenticalConstraintUuid(
        AffiliateInterface $affiliate,
        CarrierInterface $carrier,
        string $capType
    ): ?string
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

        $result = array_column($query->getScalarResult(), 'uuid');

        return count($result) > 0 ? $result[0] : null;
    }

    public function getSubscriptionConstraints()
    {
        $queryBuilder = $this->createQueryBuilder('cba');

        $query = $queryBuilder
            ->where('cba.capType = :capType')
            ->setParameter('capType', ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE)
            ->getQuery();
        return $query->getResult();
    }
}