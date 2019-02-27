<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 17:20
 */

namespace App\Domain\Repository;


use App\Domain\Entity\Carrier;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Entity\SubscriptionPack;

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
     * @param Carrier $carrier
     */
    public function increaseCounter(Carrier $carrier)
    {
        $query = $this->createQueryBuilder('c')
            ->update()
            ->set('c.counter', 'c.counter + 1')
            ->where('c.uuid = :carrierId')
            ->setParameter('carrierId', $carrier->getUuid());

        $query->getQuery()->execute();
    }

    public function updateDate(Carrier $carrier)
    {
        $query = $this->createQueryBuilder('c')
            ->update()
            ->set('c.counter', 0)
            ->set('c.flushDate', ':currentDate')
            ->set('c.isCapAlertDispatch', 0)
            ->where('c.flushDate < :currentDate')
            ->orWhere('c.flushDate IS null')
            ->andwhere('c.uuid = :carrierId')
            ->setParameters([
                'carrierId'   => $carrier->getUuid(),
                'currentDate' => date('Y-m-d')
            ]);

        $query->getQuery()->execute();
    }

    /**
     * @param int $billingCarrierId
     *
     * @return SubscriptionPack|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findActiveSubscriptionPack(int $billingCarrierId): ?SubscriptionPack
    {
        $qb = $this->getEntityManager()
            ->getRepository('SubscriptionBundle:SubscriptionPack')
            ->createQueryBuilder('sp')
            ->where('sp.carrierId = :billingCarrierId')
            ->andWhere('sp.status = :status')
            ->setParameters([
                'status'           => SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK,
                'billingCarrierId' => $billingCarrierId
            ]);
        /** @var SubscriptionPack $subscriptionPack */
        $subscriptionPack = $qb->getQuery()->getOneOrNullResult();
        return $subscriptionPack;
    }
}