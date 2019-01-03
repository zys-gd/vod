<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Carrier;

require_once dirname(__FILE__).'/CarrierApi.php';

/**
 * A small extension to the core CarrierApi to get IP from $_SERVER
 * for current client.
 *
 * The getProperties() and getProperty() methods in this class extract the 
 * most suitable client IP.
 *
 * Example usage:
 *<pre>
 * $carrierApi = new CarrierApiWeb();
 * // loading data can be slow - it is recommended wrap the API in a Singleton.
 * $carrierApi->loadDataFromFile('/path/to/sample.dat'); 
 * 
 * // get all properties for current client
 * Properties props = carrierApi.GetProperties(request);
 * 
 * // .... use the properties ....
 * if (props.ContainsKey("networkOperator") {
 *    string operatorName = props.Get("networkOperator").AsString();
 * }
 * 
 * // get a single property
 * Property countryProp = carrierApi.GetProperty("countryCode");
 * if (countryProp != null) {
 *   string countryCode = countryProp.AsString();
 * }
 *</pre>
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Carrier_CarrierApiWeb extends Mobi_Mtld_DA_Carrier_CarrierApi {
    /**
     * Get the Carrier properties for the current client.
     *
     * @return Mobi_Mtld_DA_Properties The found properties or null of no properties found.
     */
    public function getProperties($param=null) {
        return parent::getProperties($param? $param: $this->getIpFromServer());
    }    

    /**
     * Get a specific Property from the current client IP found.
     *
     * Note: if multiple properties are needed for the same IP it is more efficient
     * to call getProperties() once than repeated calls to getProperty().
     *
     * @param string propertyName The name of the property to return.
     * @return mixed The found property or null if no property found.
     * @throws Mobi_Mtld_DA_Exception_InvalidPropertyNameException
     *         Thrown if the property name does not exist.
     */
    public function getProperty($propertyName, $param=null) {
        if ($param) {
            return parent::getProperty($this->getIpFromServer(), $propertyName);
        }
        return parent::getProperty($propertyName, $param);
    }

    /**
     * Get the most suitable IP address from the given request. This function 
     * checks the headers defined in CarrierApi::HEADERS_TO_CHECK.
     * 
     * @return string The found IP address or null if no IP found.
     */
    private function getIpFromServer() {
        foreach (self::$HEADERS_TO_CHECK as $headerName) {
            $headerName = 'HTTP_'.str_replace('-', '_', strtoupper($headerName));
            if (isset($_SERVER[$headerName])) {
                $ip = $this->extractIp($headerName, $_SERVER[$headerName]);
                if ($ip) {
                    return $ip;
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']: null;
    }
}
