<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.11.19
 * Time: 16:00
 */

namespace IdentificationBundle\Identification\Service;


use IdentificationBundle\Repository\CarrierRepositoryInterface;

class CarrierResolver
{
    /**
     * @var ISPResolver
     */
    private $ISPResolver;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * CarrierResolver constructor.
     * @param ISPResolver                $ISPResolver
     * @param CarrierRepositoryInterface $carrierRepository
     */
    public function __construct(ISPResolver $ISPResolver, CarrierRepositoryInterface $carrierRepository)
    {
        $this->ISPResolver       = $ISPResolver;
        $this->carrierRepository = $carrierRepository;
    }


    /**
     * @param $carrierISP
     * @return int|null
     */
    public function resolveCarrierByISP(string $carrierISP): ?int
    {
        $carriers = $this->carrierRepository->findEnabledCarriers();
        foreach ($carriers as $carrier) {
            if ($this->ISPResolver->isISPMatches($carrierISP, $carrier)) {
                return $carrier->getBillingCarrierId();
                break;
            }
        }
        return null;
    }
}