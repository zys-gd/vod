<?php

namespace CountryCarrierDetectionBundle\Service;

use CountryCarrierDetectionBundle\Service\Interfaces\ICountryCarrierDetection;
use Symfony\Component\Cache\Adapter\AbstractAdapter;


/**
 * Class MaxMindIpInfo
 * @package CountryCarrierDetectionBundle\Service
 */
class MaxMindIpInfo implements ICountryCarrierDetection
{
    /**
     * @var CountryService
     */
    protected $countryService;
    /**
     * @var ISPService
     */
    protected $ispService;
    /**
     * @var ConnectionTypeService
     */
    protected $connectionTypeService;

    /**
     * MaxMindIpInfo constructor.
     * @param CountryService $country
     * @param ISPService $isp
     * @param ConnectionTypeService $connectionType
     */
    public function __construct(CountryService $country, ISPService $isp, ConnectionTypeService $connectionType)
    {
        $this->countryService         = $country;
        $this->ispService             = $isp;
        $this->connectionTypeService = $connectionType;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry($ipAddress = null)
    {
        if (empty($ipAddress)) {
            return $this->countryService->get();
        } else {
            return $this->countryService->getByIp($ipAddress);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getCarrier($ipAddress = null)
    {
        if (empty($ipAddress)) {
            return $this->ispService->get();
        } else {
            return $this->ispService->getByIp($ipAddress);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionType($ipAddress = null)
    {
        if (empty($ipAddress)) {
            return $this->connectionTypeService->get();
        } else {
            return $this->connectionTypeService->getByIp($ipAddress);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCellularNetwork($ipAddress = null)
    {
        return $this->getConnectionType($ipAddress) == ConnectionTypeService::CELLULAR_CONNECTION_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll($ipAddress = null)
    {
        return array(
            'country' => $this->getCountry($ipAddress),
            'carrier' => $this->getCarrier($ipAddress),
            'connection_type' => $this->getConnectionType($ipAddress)
        );
    }
}