<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 28.01.19
 * Time: 11:41
 */

namespace IdentificationBundle\Identification\Service;


use IdentificationBundle\Identification\Exception\MissingCarrierException;
use IdentificationBundle\Repository\CarrierRepositoryInterface;

class CarrierSelector
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $repository;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;


    /**
     * CarrierSelector constructor.
     * @param CarrierRepositoryInterface $repository
     * @param IdentificationDataStorage  $identificationDataStorage
     */
    public function __construct(CarrierRepositoryInterface $repository, IdentificationDataStorage $identificationDataStorage)
    {
        $this->repository                = $repository;
        $this->identificationDataStorage = $identificationDataStorage;
    }

    public function selectCarrier(int $billingCarrierId): void
    {
        if (!$this->repository->findOneByBillingId($billingCarrierId)) {
            throw new MissingCarrierException('Carrier not found');
        }

        $this->identificationDataStorage->storeCarrierId($billingCarrierId);
    }

    public function removeCarrier(): void
    {
        $this->identificationDataStorage->cleanCarrier();
    }
}