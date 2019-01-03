<?php
namespace CountryCarrierDetectionBundle\Service;

use CountryCarrierDetectionBundle\Service\MaxMindAbstract;

/**
 * Class for detecting the ISP (i.e. carrier) for the current request IP address or a give IP address.
 *
 * Class ISPService
 * @package CountryCarrierDetectionBundle\Service
 */
class ISPService extends MaxMindAbstract
{

    /**
     * Return the ISP (i.e. carrier) for the current request IP address.
     * If any errors occur or if the IP address is not listed in the database, it will return null.
     *
     * @return mixed
     */
    public function get()
    {
        // Check the country information if available in cache
        // if so, return that value instead
        $cacheKey   = $this->generateCacheKey($this->ipService->getIp(), 'ISP');
        $savedCache = $this->getAndSaveCache($cacheKey);

        if (!empty($savedCache)) {
            return $savedCache;
        }

        // Try to access the MaxMind API Reader.
        try {
            $data = $this->read();
        } catch (\Exception $e) {
            // If any errors occur, log them as critical and return null.
            $this->riseCritical($e->getMessage(), 'isp_detection', 'current_address');

            return null;
        }

        // save in cache & return the ISP value;
        // if not set, cache & return organization field, which is the same for most situations
        $results = [];
        $fields = ['isp','organization','autonomous_system_organization'];
        foreach ($fields as $field) {
            if (isset($data[$field]) && $data[$field]) {
                $results[] = $data[$field];
            }
        }
        $resultString = implode('|', $results);

        if ($resultString) {
            $this->getAndSaveCache($cacheKey, $resultString);
            return $resultString;
        } else {
            // If the IP address is not listed in database, return a null value and log the problem as warning.
            $this->riseWarning('Empty data received from service.', 'isp_detection', 'current_address');

            return null;
        }
    }

    /**
     * Return the ISP (i.e. carrier) for the IP address provided.
     * If any errors occur or if the IP address is not listed in the database, it will return null.
     *
     * @param string $ipAddress
     * @return mixed
     */
    public function getByIp($ipAddress)
    {
        // Check the country information if available in cache
        // if so, return that value instead
        $cacheKey   = $this->generateCacheKey($ipAddress, 'ISP');
        $savedCache = $this->getAndSaveCache($cacheKey);

        if (!empty($savedCache)) {
            return $savedCache;
        }

        // Try to access the MaxMind API Reader.
        try {
            $data = $this->readByIp($ipAddress);
        } catch (\Exception $e) {

            // If any errors occur, log them as critical and return null.
            $this->riseCritical($e->getMessage(), 'isp_detection', 'custom_address');

            return null;
        }

        // save in cache & return the ISP value;
        // if not set, cache & return organization field, which is the same for most situations
        if (isset($data['isp'])) {
            $this->getAndSaveCache($cacheKey, $data['isp']);

            return $data['isp'];
        } elseif (isset($data['organization'])) {
            $this->getAndSaveCache($cacheKey, $data['organization']);

            return $data['organization'];
        } else {

            // If the IP address is not listed in database, return a null value and log the problem as warning.
            $this->riseWarning('Empty data received from service.', 'isp_detection', 'custom_address');

            return null;
        }
    }
}