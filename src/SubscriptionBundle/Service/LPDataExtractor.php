<?php


namespace SubscriptionBundle\Service;


use App\Domain\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use IdentificationBundle\Repository\CarrierRepositoryInterface;

class LPDataExtractor
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var CountryRepository
     */
    private $countryRepository;

    /**
     * LPDataExtractor constructor.
     * @param CarrierRepositoryInterface $carrierRepository
     * @param CountryRepository          $countryRepository
     */
    public function __construct(CarrierRepositoryInterface $carrierRepository, CountryRepository $countryRepository)
    {
        $this->carrierRepository = $carrierRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @return ArrayCollection
     */
    public function getActiveCarrierCountries()
    {
        $carriersCountryCodes = $this->carrierRepository->findEnabledCarriersCountryCodes();

        $activeCountries = $this->countryRepository->findBy(['countryCode' => $carriersCountryCodes]);

        return new ArrayCollection($activeCountries);
    }
}