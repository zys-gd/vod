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

class CarrierRepository extends \Doctrine\ORM\EntityRepository implements CarrierRepositoryInterface
{

    /**
     * @return CarrierInterface[]
     */
    public function findAllCarriers(): array
    {
        return $this->findAll();
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
                'carrierId' => $carrier->getUuid(),
                'currentDate' => date('Y-m-d')
            ]);

        $query->getQuery()->execute();
    }
}