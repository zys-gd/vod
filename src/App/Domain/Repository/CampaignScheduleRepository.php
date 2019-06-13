<?php


namespace App\Domain\Repository;

use Doctrine\ORM\EntityRepository;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;

class CampaignScheduleRepository extends EntityRepository
{
    /**
     * @param CampaignInterface $campaign
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNowInSchedule(CampaignInterface $campaign)
    {
        $currentTime      = date('H:i');
        $currentDayNumber = date('N');

        $queryBuilder = $this->createQueryBuilder('cs');

        $query = $queryBuilder
            ->select('COUNT(cs.uuid)')
            ->where('cs.campaign = :campaign')
            ->andWhere('(cs.dayStart < :currentDayNumber OR (cs.dayStart = :currentDayNumber AND cs.timeStart <= :currentTime))')
            ->andWhere('(cs.dayEnd > :currentDayNumber OR (cs.dayEnd = :currentDayNumber AND cs.timeEnd >= :currentTime))')
            ->setParameter('campaign', $campaign)
            ->setParameter('currentDayNumber', $currentDayNumber)
            ->setParameter('currentTime', $currentTime)
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}