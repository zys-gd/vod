<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Carrier;

if (!defined('Mobi_Mtld_DA_CARRIER_API_PATH')) {
    define('Mobi_Mtld_DA_CARRIER_API_PATH', dirname(__FILE__).'/');
}
if (!defined('Mobi_Mtld_DA_COMMON_API_PATH')) {
    define('Mobi_Mtld_DA_COMMON_API_PATH', Mobi_Mtld_DA_CARRIER_API_PATH.'../');
}

require_once Mobi_Mtld_DA_CARRIER_API_PATH.'CarrierData.php';

/**
 * The main class for the Carrier Identification API. Used to load the data file and 
 * to perform lookups using IPv4 addresses. For usage in a Web App it is
 * recommended to use the CarrierApiWeb (CarrierApiWeb.php) class so you don't
 * have to pass the client's IP address manually.
 * 
 * Please note that it is advisable to load only a single instance of this class
 * to avoid multiple loadings of the data file.
 * 
 * Example usage:
 *<pre>
 *     $ip = '62.40.34.220';
 *     $carrierApi = new CarrierApi();
 *     $carrierApi->loadDataFromFile('/path/to/sample.dat');
 *
 *     // get all properties
 *     $props = $carrierApi->getProperties($ip);
 *     // .... use the properties ....
 *     if ($props->containsKey('networkOperator')) {
 *         $property = $props->get('networkOperator');
 *         $operatorName = $property->asString();
 *         print('networkOperator: ' . $operatorName);
 *     }
 *     
 *     // get a single property
 *     $mccProp = $carrierApi->getProperty($ip, 'mcc');
 *     if ($mccProp !== null) {
 *         $mcc = $mccProp->asString();
 *         print('MCC: ' . $mcc);
 *     }
 *</pre>
 * Please see the code in the Examples directory for additional samples.
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Carrier_CarrierApi {
    private $ipSpecial = null;
    /** Carrier API version */
    const VERSION         = 'API-VERSION';
    /** Min PHP version required for this API to work */
    const MIN_PHP_VERSION = '5.2.3';

    /**
     * A list of HTTP headers to choose the original client IP address from. In
     * addition to these the RemoteAddr (REMOTE_ADDR) is also used as a final
     * fallback.
     */
    public static $HEADERS_TO_CHECK = array(
        'x-forwarded-for',
        'client-ip', 
        'x-client-ip', 
        'rlnclientipaddr',    // from f5 load balancers
        'proxy-client-ip',
        'wl-proxy-client-ip', // weblogic load balancers
        'x-forwarded',
        'forwarded-for',
        'forwarded',
    );
    /** Data loaded from the data file */
    private $data;
    
    /** Error messages */
    private static $MISSING_DATA_EX = 'No data file loaded, load data with loadDataFromFile().';
    /** Error messages */
    private static $INVALID_PROP_EX = 'Property name "%s" does not exist.';

    /**
     * Construct a Carrier API object and load the data file (if path is provided).
     *
     * @throws Exception If PHP version is less than required
     */
    public function __construct() {
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION) < 0) {
            throw new Exception(
                'DeviceAtlas Carrier API requires PHP version ' .
                self::MIN_PHP_VERSION . ' or later.'
            );
        }
    }

    /**
     * Load the data file from the provided path. The data file is reloaded 
     * every time this method is called.
     *
     * @param string path The path on disk to the carrier data file.
     * @throws IOException Thrown when there is a problem loading the file.
     * @throws Mobi_Mtld_DA_Exception_DataFileException Thrown when there is a problem with the data file.
     */
    public function loadDataFromFile($path) {
        $newData = new Mobi_Mtld_DA_Carrier_CarrierData();
        $newData->loadDataFromFile($path);
        $this->data = $newData;
    }

    /**
     * Returns the data file copyright text.
     * 
     * @return string the copyright
     */
    public function getDataFileCopyright() {
        $this->dataLoaded();
        return $this->data->getCopyright();
    }

    /**
     * Returns the data file creation date in ISO8601 format.
     * @return string the creationDate
     */
    public function getDataFileCreationDate() {
        $this->dataLoaded();
        return $this->data->getCreationDate();
    }

    /**
     * Returns the version of the data file.
     * @return string the version
     */
    public function getDataFileVersion() {
        $this->dataLoaded();
        return $this->data->getVersion();
    }

    /**
     * @overload
     * Get the Carrier properties for a given IP address.
     * 
     * @param string|int ipv4 The IP address to find carrier properties for.
     * @return Mobi_Mtld_DA_Properties The found properties or null of no properties found.
     *
     * @overload
     * Get the Carrier properties for a given set of HTTP headers.
     * 
     * @param array ipv4 An array of HTTP headers, the keys will be normalized.
     * @return Mobi_Mtld_DA_Properties The found properties or null of no properties found.
     */
    public function getProperties($ipv4) {
        if (is_array($ipv4)) {
            $ipv4 = $this->getIp($ipv4);
        }
        $this->dataLoaded();
        $props = null;

        if ($ipv4 != null) {
            $props = $this->data->getProperties($ipv4);

            if ($this->ipSpecial != null) {
                if ($props == null || !(
                    $props->containsKey("networkBrand") &&
                    $props->containsKey("countryCode") &&
                    $props->get("networkBrand")->asString() == "T-Mobile" &&
                    $props->get("countryCode")->asString() == "DE")
                    ) {
                    $props = $this->data->getProperties($this->ipSpecial);
                }
            }
        }

        $this->ipSpecial = null;
        return $props;
    }

    /**
     * @overload
     * Try and get a specific property for a given IP address.
     * 
     * Note: if multiple properties are needed for the same IP it is more efficient
     * to call getProperties() once than repeated calls to getProperty().
     * 
     * @param string|int ipv4 The IP address to find carrier properties for.
     * @param string propertyName The name of the property to return
     * @return Mobi_Mtld_DA_Property The Property or null if no property found.
     * @throws Mobi_Mtld_DA_Exception_InvalidPropertyNameException 
     *         Thrown if the property name does not exist.
     *
     * @overload
     * Try and get a specific property for a given IP address.
     * 
     * Note: if multiple properties are needed for the same IP it is more efficient
     * to call getProperties() once than repeated calls to getProperty().
     * 
     * @param array ipv4 An array of HTTP headers, the keys will be normalized.
     * @param string propertyName The name of the property to return
     * @return Mobi_Mtld_DA_Property The Property or null if no property found.
     * @throws Mobi_Mtld_DA_Exception_InvalidPropertyNameException 
     *         Thrown if the property name does not exist.
     */
    public function getProperty($ipv4, $propertyName) {
        if (is_array($ipv4)) {
            $ipv4 = $this->getIp($ipv4);
        }

        $this->dataLoaded();
        $this->propertyNameExists($propertyName);

        $property = null;
        if ($ipv4 !== null) {
            $props = $this->data->getProperties($ipv4);
            if ($props !== null && $props->containsKey($propertyName)) {
                $property = $props->get($propertyName);
            }
        }

        return $property;
    }

    /**
     * A set of all the possible property names. The Set contains PropertyName
     * objects that each have a name and an associated data type.
     * @return array array of possible property names.
     */
    public function getPropertyNames() {
        $this->dataLoaded();
        return $this->data->getPropertyNames();
    }

    /**
     * Check if the given propertyName is not null and exists in the data file.
     */
    protected function dataLoaded() {
        if ($this->data === null) {
            require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/MissingDataException.php';
            throw new Mobi_Mtld_DA_Exception_MissingDataException(self::$MISSING_DATA_EX);
        }
    }

    /**
     * Check if the given propertyName is not null and exists in the data file. Calls
     * to this method must be sure that the data object is already loaded.
     * 
     * @throws Mobi_Mtld_DA_Exception_InvalidPropertyNameException  
     */
    protected function propertyNameExists($propertyName) {
        $propertyNames = $this->data->getPropertyNamesAsStrings();

        if ($propertyNames === null || !in_array($propertyName, $propertyNames)) {
            require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/InvalidPropertyNameException.php';
            throw new Mobi_Mtld_DA_Exception_InvalidPropertyNameException(
                sprintf(self::$INVALID_PROP_EX, $propertyName)
            );
        }
    }

    /**
     * Get the most suitable IP address from the given keyVals set of HTTP 
     * headers. This function checks the headers defined in HEADERS_TO_CHECK.
     * 
     * @param array keyVals array of headers
     * @return string The most suitable IP or NULL if a suitable IP could not be found.
     */
    public function getIp($keyVals) {
        if ($keyVals) {
            // normalize header keys
            foreach ($keyVals as $k => $v) {
                $k = str_replace('_', '-', strtolower($k));
                if (substr($k, 0, 5) === 'http-') {
                    $k = substr($k, 5);
                } 
                $headers[trim($k)] = $v;
            }
            // lookup headers
            foreach (self::$HEADERS_TO_CHECK as $headerName) {
                if (isset($headers[$headerName])) {
                    $value = $headers[$headerName];
                    if ($value) {
                        $ip = $this->extractIp($headerName, $value);
                        if ($ip) {
                            return $ip;
                        }
                    }
                }
            }
        }

        // the only public ip found was a special ip, consider it as the found
        if ($this->ipSpecial != null) {
            $ip = $this->ipSpecial;
            $this->ipSpecial = null;
            return $ip;
        }

        return null;
    }

    /**
     * Extracts and cleans an IP address from the headerValue. Some headers such 
     * as "X-Forwarded-For" can contain multiple IP addresses such as: 
     * clientIP, proxy1, proxy2...
     * 
     * This method splits up the headerValue and takes the most appropriate 
     * value as the IP.
     */
    protected function extractIp($headerName, $headerValue) {
        if ($headerValue !== null) {
            // "X-Forwarded-For" header can contain many comma seperated IPs such as clientIP, proxy1, proxy2...
            // we are interested in the first item
            if ($headerName === 'x-forwarded-for') {
				// loop over all parts and take the first non-empty value
				foreach (explode(',', $headerValue) as $part) {
					$part = trim($part);
					if ($part && $this->isPublicIp($part) && !$this->isIpSpecial($part)) {
						return $part;
					}
				}
            } else {
                $headerValue = trim($headerValue);
                if ($this->isPublicIp($headerValue) && !$this->isIpSpecial($headerValue)) {
                    return $headerValue;
                }
            }
        }
    
        return null;
    }

    /**
     * Check if ip is a special case return true and put the value in the ipSpecial property
     */
    private function isIpSpecial($ip) {
        $ippart = (int)(explode('.', $ip)[0]);
        if ($ippart > 10 && $ippart < 15) {
            $this->ipSpecial = $ip;
            return true;
        }

        return false;
    }

    /**
     * An IP address is considered public if it is not in any of the following
     * ranges:
     *<pre>
     *  1) any local address
     *     IP:  0
     *
     *  2) a local loopback address
     *     range:  127/8
     *  
     *  3) a site local address i.e. IP is in any of the ranges:
     *     range:  10/8 
     *     range:  172.16/12 
     *     range:  192.168/16
     *  
     *  4) a link local address 
     *     range:  169.254/16
     *</pre>
     * 
     * @param string ip The IP address to check
     * @return boolean True if it is a public IP, false if the IP is invalid or is not public.
     */
    public function isPublicIp($ip) {
        $ip = ip2long($ip);
		return (0          != ($ip & 4278190080)) // 0.0.0.0/8
			&& (2130706432 != ($ip & 4278190080)) // 127.0.0.0/8
			&& (3232235520 != ($ip & 4294901760)) // 192.168.0.0/16
			&& (2886729728 != ($ip & 4293918720)) // 172.16.0.0/12
			&& (167772160  != ($ip & 4278190080)) // 10.0.0.0/8
			&& (2851995648 != ($ip & 4294901760)) // 169.254.0.0/16
			&& (1681915904 != ($ip & 4290772992)) // 100.64.0.0/10
			&& (3221225472 != ($ip & 4294967288)) // 192.0.0.0/29
			&& (3221225984 != ($ip & 4294967040)) // 192.0.2.0/24
			&& (3227017984 != ($ip & 4294967040)) // 192.88.99.0/24
			&& (3323068416 != ($ip & 4294836224)) // 198.18.0.0/15
			&& (3325256704 != ($ip & 4294967040)) // 198.51.100.0/24
			&& (3405803776 != ($ip & 4294967040)) // 203.0.113.0/24
			&& (3758096384 != ($ip & 4026531840)) // 224.0.0.0/4
			&& (4026531840 != ($ip & 4026531840));// 240.0.0.0/4
    }
}
