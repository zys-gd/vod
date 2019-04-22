<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Affiliate;
use Doctrine\ORM\EntityRepository;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;

/**
 * Class AffiliateRepository
 */
class AffiliateRepository extends EntityRepository
{
    /**
     * @param AffiliateInterface|Affiliate $affiliate
     */
    public function switchStatusRelatedCampaigns(AffiliateInterface $affiliate)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->update('App\Domain\Entity\Campaign', 'c')
            ->set('c.isPause', ':isPause')
            ->where('c.affiliate = :affiliate')
            ->setParameter('isPause', !$affiliate->getEnabled())
            ->setParameter('affiliate', $affiliate);
        $qb->getQuery()->execute();
    }
}