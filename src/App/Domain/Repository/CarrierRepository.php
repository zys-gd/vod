<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 17:20
 */

namespace App\Domain\Repository;


use CommonDataBundle\Entity\Country;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\AbstractQuery;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Entity\SubscriptionPack;
use function Doctrine\ORM\QueryBuilder;

class CarrierRepository extends \Doctrine\ORM\EntityRepository implements CarrierRepositoryInterface
{

    /**
     * @return CarrierInterface[]
     */
    public function findEnabledCarriers(): array
    {
        return $this->findBy(['published' => true]);
    }


    /**
     * @param int $billingCarrierId
     *
     * @return CarrierInterface|null
     */
    public function findOneByBillingId(int $billingCarrierId): ?CarrierInterface
    {
        return $this->findOneBy(['billingCarrierId' => $billingCarrierId]);
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return SubscriptionPack|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findActiveSubscriptionPack(CarrierInterface $carrier): ?SubscriptionPack
    {
        $qb = $this->getEntityManager()
            ->getRepository('SubscriptionBundle:SubscriptionPack')
            ->createQueryBuilder('sp')
            ->where('sp.carrier = :carrier')
            ->andWhere('sp.status = :status')
            ->setParameters([
                'status'  => SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK,
                'carrier' => $carrier
            ]);
        /** @var SubscriptionPack $subscriptionPack */
        $subscriptionPack = $qb->getQuery()->getOneOrNullResult();
        return $subscriptionPack;
    }

    /**
     * @return array
     */
    public function findEnabledCarriersCountries(): array
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where($qb->expr()->eq('v.published', true))
            ->innerJoin(Country::class, 'c','WITH', 'c.countryCode = v.countryCode')
            ->select('c')
            ->groupBy('c.countryCode');

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
    }
}