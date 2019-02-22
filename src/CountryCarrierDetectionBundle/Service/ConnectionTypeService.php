<?php
namespace CountryCarrierDetectionBundle\Service;

/**
 * Class for detecting the Connection Type (e.g. mobile, residential, military etc.)
 *
 * Class ConnectionTypeService
 * @package CountryCarrierDetectionBundle\Service
 */
class ConnectionTypeService extends MaxMindAbstract
{

    /**
     * The name of connection type for users which have a carrier.
     */
    const CELLULAR_CONNECTION_TYPE = 'Cellular';

    /**
     * Return the connection type (e.g. residential, cellular, wifi etc.) for the current request IP address.
     * If any errors occur or if the IP address is not listed in the database, it will return null.
     *
     * @see http://dev.maxmind.com/geoip/geoip2/web-services/#traits (complete list of connection types available)
     * @return mixed
     */
    public function get()
    {
        // Check the country information if available in cache
        // if so, return that value instead
        $cacheKey   = $this->generateCacheKey($this->ipService->getIp(), 'connection_type');
        $savedCache = $this->getAndSaveCache($cacheKey);

        if (!empty($savedCache)) {
            return $savedCache;
        }

        // Try to access the MaxMind API Reader.
        try {
            $data = $this->read();
        } catch (\Exception $e) {
            // If any errors occur, log them as critical and return null.
            $this->riseCritical($e->getMessage(), 'connection_type_detection', 'current_address');

            return null;
        }

        // If the IP address is not listed in database, return a null value and log the problem as warning.
        if (!isset($data['connection_type'])) {
            $this->riseWarning('Empty data received from service.', 'connection_type_detection', 'current_address');

            return null;
        }

        // Save the data in cache
        $this->getAndSaveCache($cacheKey, $data['connection_type']);

        return $data['connection_type'];
    }

    /**
     * Return the connection type (e.g. residential, cellular, wifi etc.) for the IP address provided.
     * If any errors occur or if the IP address is not listed in the database, it will return null.
     *
     * @see http://dev.maxmind.com/geoip/geoip2/web-services/#traits (complete list of connection types available)
     * @param string $ipAddress
     * @return mixed
     */
    public function getByIp($ipAddress)
    {
        // Check the country information if available in cache
        // if so, return that value instead
        $cacheKey   = $this->generateCacheKey($ipAddress, 'connection_type');
        $savedCache = $this->getAndSaveCache($cacheKey);

        if (!empty($savedCache)) {
            return $savedCache;
        }

        // Try to access the MaxMind API Reader.
        try {
            $data = $this->readByIp($ipAddress);
        } catch (\Exception $e) {
            // If any errors occur, log them as critical and return null.
            $this->riseCritical($e->getMessage(), 'connection_type_detection', 'custom_address');

            return null;
        }

        // If the IP address is not listed in database, return a null value and log the problem as warning.
        if (!isset($data['connection_type'])) {
            $this->riseWarning('Empty data received from service.', 'connection_type_detection', 'custom_address');

            return null;
        }

        // Save the data in cache
        $this->getAndSaveCache($cacheKey, $data['connection_type']);

        return $data['connection_type'];
    }
}