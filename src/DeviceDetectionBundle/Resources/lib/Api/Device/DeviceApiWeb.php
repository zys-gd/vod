<?php
/*
 * package Mobi\Mtld\DA\Device
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Device;
require_once dirname(__FILE__).'/DeviceApi.php';

/**
 * A small extension to the core DeviceApi that uses the request HTTP headers
 * for detection and property lookup.<br/><br/>
 *
 * When detecting user's device as real-time on a website it is strongly recommend
 * to use the DeviceApiWeb library and getProperties as shown bellow.<br/><br/>
 *
 * The getProperties of DeviceApiWeb, automatically detects the availability
 * of the client-side properties and will use them for detection. If you want
 * to have a more accurate detection (specially for apple devices) or have the
 * client side properties included in the property sets, simply include the
 * DeviceAtlas client-side component JavaScript library in the pages.<br/><br/>
 *
 * The API may be configured configured to improve performance and memory footprint,
 * when used in a web application for real-time device detection:<br/><br/>
 * 1. Optimizing the data file.<br/>
 * 2. Caching the results.<br/><br/>
 *
 * The getProperties() method in this class extracts the most suitable headers
 * from the request headers.<br/><br/>
 *
 * Example usage:<br/>
 * <pre>
 * require_once "/PATH/TO/Mobi/Mtld/DeviceApiWeb.php"; 
 * // default=the data optimization is on
 * $deviceApi = new Mobi_Mtld_DA_Device_DeviceApiWeb();
 * try {
 *     $deviceApi->loadDataFromFile("/path/to/datafile.json"); 
 *
 *     // get all properties from the current request
 *     $properties = $deviceApi->getProperties();
 *
 *     // .... use the properties ....
 *     if ($properties->contains("mobileDevice", true)) {
 *         if ($properties->containsKey("model")) {
 *             $deviceModel = $properties->model;
 *         }
 *     }
 * } catch (...
 * </pre>
 *
 * @package Mobi\Mtld\DA\Device
 * @version 2.0.0
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_Device_DeviceApiWeb extends Mobi_Mtld_DA_Device_DeviceApi {
    /**
     * Constructs a DeviceApiWeb instance with custom configs.
     * @see Mobi_Mtld_DA_Device_Config
     *
     * @param Mobi_Mtld_DA_Device_Config config An instance of Config, you can change the
     * DeviceAtlas API configs by creating an instance or Config and setting your
     * preferences config values then passing the instance to the DeviceApi constructor.
     * null=use default configs.
     */
    public function __construct($config=null) {
        $this->setConfig($config);
    }
    
    /**
     * Set new config settings via a new Config object and drop current cache.
     * 
     * @param Mobi_Mtld_DA_Device_Config $config
     */
    public function setConfig(Mobi_Mtld_DA_Device_Config $config = null) {
        if (!$config) {
            $this->config = new Mobi_Mtld_DA_Device_Config();
            $this->config->setUseTreeOptimizer(true);
        } else {
            $this->config = $config;
        }
        if ($this->tree) {
            $this->tree->setConfig($this->config);
        }
    }

    /**
     * Get the Device properties for the current request. The most suitable request
     * headers are extracted and used for detection and property lookup.<br/>
     * If the client-side-component has been used and the client-side properties
     * exists in the client-side cookie, then the properties will be included.<br/>
     * You can change the API's configs using class "Mobi_Mtld_DA_Device_Config".
     * Depending on the configs the properties may include user-agent dynamic
     * properties and language/locale properties.
     * 
     * @return The found properties
     * @throws Mobi_Mtld_DA_Esception_ClientPropertiesException
     */
    public function getProperties($headers=null, $clientSideProperties=null) {
        if ($headers === null) {
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
            } else {
                $headers = array();
                foreach ($_SERVER as $key => $value) {
                    if (substr($key, 0, 5) === 'HTTP_') {
                        $headers[$key] = $value;
                    }
                }
            }
        }

        if (!$clientSideProperties) {
            $cookieName = $this->config->getCookieName();
            $clientSideProperties = isset($_COOKIE[$cookieName])? $_COOKIE[$cookieName]: null;
        }

        return parent::getProperties($headers, $clientSideProperties);
    }    
}
