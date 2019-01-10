<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 17:14
 */

namespace IdentificationBundle\Repository;


use IdentificationBundle\Entity\CarrierInterface;

interface CarrierRepositoryInterface
{

    public function findOneByBillingId(int $billingCarrierId): ?CarrierInterface;

    /**
     * @return CarrierInterface[]
     */
    public function findAllCarriers(): array;

}