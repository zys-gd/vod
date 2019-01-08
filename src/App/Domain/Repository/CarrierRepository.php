<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 17:20
 */

namespace App\Domain\Repository;


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

    public function findOneByBillingId(int $billingId): ?CarrierInterface
    {
        return $this->findOneBy(['billingCarrierId' => $billingId]);
    }
}