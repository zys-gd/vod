<?php
namespace CountryCarrierDetectionBundle\Service\Interfaces;

/**
 * Interface for any country/carrier detection service layer.
 *
 * Interface ICountryCarrierDetection
 * @package CountryCarrierDetectionBundle\Service\Interfaces
 */
interface ICountryCarrierDetection
{
    /**
     * Fetch all data as an associative array, containing Country, Carrier and Connection Type information.
     * If $ipAddress is not provided, it will use the IP address of the current request.
     *
     * @param string $ipAddress
     * @return array
     */
    public function getAll($ipAddress = null);

    /**
     * Return the 2-letters country code for the IP address provided.
     * If $ipAddress is not provided, it will use the IP address of the current request.
     *
     * @param string $ipAddress
     * @return string
     */
    public function getCountry($ipAddress = null);

    /**
     * Return the carrier (ISP) for the IP address provided.
     * If $ipAddress is not provided, it will use the IP address of the current request.
     *
     * @param string $ipAddress
     * @return string
     */
    public function getCarrier($ipAddress = null);

    /**
     * Return the connection type (e.g. cellular, residential, wifi etc.)
     * If $ipAddress is not provided, it will use the IP address of the current request.
     *
     * @param string $ipAddress
     * @return string
     */
    public function getConnectionType($ipAddress = null);

    /**
     * Check and return if the IP address provided is a cellular (i.e. mobile phone connection) network.
     * If $ipAddress is not provided, it will use the IP address of the current request.
     *
     * @param string $ipAddress
     * @return bool
     */
    public function isCellularNetwork($ipAddress = null);
}