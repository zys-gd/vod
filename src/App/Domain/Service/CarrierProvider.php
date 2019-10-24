<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.10.19
 * Time: 14:13
 */

namespace App\Domain\Service;


use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use ExtrasBundle\Cache\ArrayCache\ArrayCacheService;

class CarrierProvider
{
    /**
     * @var ArrayCacheService
     */
    private $arrayCacheService;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;


    /**
     * CarrierProvider constructor.
     */
    public function __construct(ArrayCacheService $arrayCacheService, CarrierRepository $carrierRepository)
    {
        $this->arrayCacheService = $arrayCacheService;
        $this->carrierRepository = $carrierRepository;
    }

    public function fetchCarrierIfNeeded(int $billingCarrierId): Carrier
    {
        $key = sprintf('%s_%s', $billingCarrierId, 'carrier');

        if ($this->arrayCacheService->hasCache($key)) {
            $carrier = $this->arrayCacheService->getValue($key);
        } else {
            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            $this->arrayCacheService->saveCache($key, $carrier);
        }

        if (!$carrier) {
            throw new \InvalidArgumentException(sprintf('Carrier %s is not found', $billingCarrierId));
        }

        return $carrier;
    }

}