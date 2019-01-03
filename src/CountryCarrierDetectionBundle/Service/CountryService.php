<?php
namespace CountryCarrierDetectionBundle\Service;

use CountryCarrierDetectionBundle\Service\MaxMindAbstract;

/**
 * Class for detecting the country (2-letters code) for an IP address.
 *
 * Class CountryService
 * @package CountryCarrierDetectionBundle\Service
 */
class CountryService extends MaxMindAbstract
{

    /**
     * Return the 2-letters country code for the IP address of current request (e.g. GB, US, FR etc.)
     * If any errors occur or if the IP address is not listed in the database, it will return null.
     *
     * @return mixed
     */
    public function get()
    {
        // Check the country information if available in cache
        // if so, return that value instead
        $cacheKey   = $this->generateCacheKey($this->ipService->getIp(), 'country');
        $savedCache = $this->getAndSaveCache($cacheKey);

        if (!empty($savedCache)) {
            return $savedCache;
        }

        // Try to access the MaxMind API Reader.
        try {
            $data = $this->read();
        } catch (\Exception $e) {
            // If any errors occur, log them as critical and return null.
            $this->riseCritical($e->getMessage(), 'country_detection', 'current_address');

            return null;
        }

        // If the IP address is not listed in database, return a null value and log the problem as warning.
        if (!isset($data['country']['iso_code'])) {
            $this->riseWarning('Empty data received from service.', 'country_detection', 'current_address');

            return null;
        }

        // Save the data in cache
        $this->getAndSaveCache($cacheKey, $data['country']['iso_code']);

        return $data['country']['iso_code'];

    }

    /**
     * Return the 2-letters country code for the IP address provided (e.g. GB, US, FR etc.)
     * If any errors occur or if the IP address is not listed in the database, it will return null.
     *
     * @param string $ipAddress
     * @return mixed
     */
    public function getByIp($ipAddress)
    {
        // Check the country information if available in cache
        // if so, return that value instead
        $cacheKey   = $this->generateCacheKey($ipAddress, 'country');
        $savedCache = $this->getAndSaveCache($cacheKey);

        if (!empty($savedCache)) {
            return $savedCache;
        }

        // Try to access the MaxMind API Reader.
        try {
            $data = $this->readByIp($ipAddress);
        } catch (\Exception $e) {
            // If any errors occur, log them as critical and return null.
            $this->riseCritical($e->getMessage(), 'country_detection', 'custom_address');

            return null;
        }

        // If the IP address is not listed in database, return a null value and log the problem as warning.
        if (!isset($data['country']['iso_code'])) {
            $this->riseWarning('Empty data received from service.', 'country_detection', 'custom_address');

            return null;
        }

        // Save the data in cache
        $this->getAndSaveCache($cacheKey, $data['country']['iso_code']);

        return $data['country']['iso_code'];
    }
}