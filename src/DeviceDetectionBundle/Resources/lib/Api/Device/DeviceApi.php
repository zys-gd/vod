<?php
/*
 * package Mobi\Mtld\DA\Device
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Device;

if (!defined('Mobi_Mtld_DA_DEVICE_API_PATH')) {
    define('Mobi_Mtld_DA_DEVICE_API_PATH', dirname(__FILE__).'/');
}
if (!defined('Mobi_Mtld_DA_COMMON_API_PATH')) {
    define('Mobi_Mtld_DA_COMMON_API_PATH', Mobi_Mtld_DA_DEVICE_API_PATH.'../');
}

require_once Mobi_Mtld_DA_DEVICE_API_PATH.'Config.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'PropertyName.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'DataType.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'CacheProvider/VolatileCacheProvider.php';

/**
 * The DeviceAtlas device detection API provides a way to detect devices based
 * on the HTTP headers. Using the headers, the API returns device properties such
 * as screen width, screen height, is mobile, vendor, model etc. If you want to
 * use the DeviceAtlas API for web applications then it is recommended to use
 * DeviceApiWeb instead of this library.<br/>
 * The DeviceApiWeb is preferred when you want to get the properties from a
 * real-time detection on user's device in a web application.
 * The DeviceApi is preferred when you want to get the properties from an
 * off-line user-agent list or header set.<br/><br/>
 *
 * To get the most accurate results: 1- Pass the whole HTTP headers.
 * 2- Use the DeviceAtlas client-side-component and pass the result.<br/><br/>
 *
 * Valid inputs are:<br/>
 * $deviceApi->getProperties((array) headers, string clientSide)<br/>
 * $deviceApi->getProperties((array) headers)<br/>
 * $deviceApi->getProperties(string userAgent, string clientSide)<br/>
 * $deviceApi->getProperties(string userAgent)<br/><br/>
 *
 * Example usage:<br/>
 * <pre>
 * $headers = array(
 *     "user-agent"      => "...",
 *     "accept-language" => "...",
 *     // add any other available header...
 * );
 *
 * require_once "/PATH/TO/Api/Device/DeviceApi.php";
 * $deviceApi = new Mobi_Mtld_DA_Device_DeviceApi();
 *
 * try {
 *     $deviceApi->loadDataFromFile("/path/to/datafile.json");
 *
 *     // get all properties from the headers
 *     $props = $deviceApi->getProperties($headers);
 *
 *     // .... use the properties ....
 *     if ($props->contains("isMobilePhone", true)) {
 *         // it is a mobile phone
 *     }
 *
 *     // check if property exists then get the property value
 *     $displayWidth = $props->containsKey("displayWidth")?
 *         $props->get("displayWidth")->asInteger(): 100;
 *
 *     // the most easy way to get a property value
 *     $deviceModel = $props->model; // null or string
 * } catch (...
 * </pre>
 *
 * @package Mobi\Mtld\DA\Device
 * @version 2.1.0
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_Device_DeviceApi {
    /** Api version. */
    const API_VERSION     = '2.1';
    /** Minimum PHP version required for this API to run. **/
	const MIN_PHP_VERSION = '5.2.3';

    const LOOKUP_SOURCE_TREE           = 'tree';
    const LOOKUP_SOURCE_OPTIMIZED_TREE = 'optimized tree';
    const LOOKUP_SOURCE_CACHE          = 'cache';

    protected static $LANGUAGE_HEADER               = 'accept-language';
    private   static $PROPERTY_NAME_LANGUAGE        = 'language';
    private   static $PROPERTY_NAME_LANGUAGE_LOCALE = 'languageLocale';

    protected $config; // config object contains API config params
    protected $tree;   // tree object handles the json tree

    /**
     * Constructs a DeviceApi instance with custom configs.
     * @see Mobi_Mtld_DA_Device_Config
     *
     * @param Mobi_Mtld_DA_Device_Config config An instance of Config, you can change the
     * DeviceAtlas API configs by creating an instance or Config and setting your
     * preferences config values then passing the instance to the DeviceApi constructor.
     * null=use default configs.
     */
    public function __construct(Mobi_Mtld_DA_Device_Config $config = null) {
        $this->setConfig($config);
    }

    /**
     * Set new config settings via a new Config object and drop current cache.
     *
     * @param Mobi_Mtld_DA_Device_Config $config
     */
    public function setConfig(Mobi_Mtld_DA_Device_Config $config = null) {

        // If there are no custom configs,
        // add volatile cache
        if (!$config) {
            $this->config = new Mobi_Mtld_DA_Device_Config();
            $cacheProvider = new Mobi_Mtld_DA_CacheProvider_VolatileCacheProvider();
            $this->config->setCacheProvider($cacheProvider);

        // If there are custom configs
        // and if it uses volatile cache,
        // clear it
        } else {
            $cacheProvider = $config->getCacheProvider();
            if ($cacheProvider && is_a($cacheProvider, 'Mobi_Mtld_DA_CacheProvider_VolatileCacheProvider')) {
                $cacheProvider->clear();
            }
            $this->config = clone($config);
        }

        // If tree has been created,
        // pass configs to it
        if ($this->tree) {
            $this->tree->setConfig($this->config);
        }

    }

    /**
     * Load the DeviceAtlas device detection data into the API from a JSON file.
     * The JSON data file is provided by the DeviceAtlas web-site.
     *
     * @param string jsonDataFilePath Path to the JSON file
     * @throws IOException
     * @throws Mobi_Mtld_DA_Exception_DataFileException
     * @throws Mobi_Mtld_DA_Exception_DataReadException
     * @throws Mobi_Mtld_DA_Exception_JsonException
     */
    public function loadDataFromFile($jsonDataFilePath) {
        $cacheProvider = $this->config->getCacheProvider();
        $useExternalCache = $cacheProvider &&
                !is_a($cacheProvider, 'Mobi_Mtld_DA_CacheProvider_VolatileCacheProvider');

        // use external cache and tree optimizations
        if ($useExternalCache || $this->config->getUseTreeOptimizer()) {
            require_once Mobi_Mtld_DA_DEVICE_API_PATH.'TreeOptimized.php';
            $this->tree = new Mobi_Mtld_DA_Device_TreeOptimized($this->config);
        // use normal tree
        } else {
            require_once Mobi_Mtld_DA_DEVICE_API_PATH.'Tree.php';
            $this->tree = new Mobi_Mtld_DA_Device_Tree($this->config);
        }

        $this->tree->loadTreeFromFile($jsonDataFilePath);

    }

    /**
     * Load the DeviceAtlas device detection data into the API from a string.
     * The JSON data file is provided by the DeviceAtlas web-site.
     *
     * @param string data DeviceAtlas device data
     * @throws Mobi_Mtld_DA_Exception_DataFileException
     * @throws Mobi_Mtld_DA_Exception_JsonException
     */
    public function loadDataFromString($data) {
        $this->tree = new Mobi_Mtld_DA_Device_Tree($this->config);
        $this->tree->loadTreeFromString($data);

        $this->cachedProperties = array();
    }

    /**
     * Get a set of available device property names.
     *
     * @return array A set of available device property names as PropertyName objects.
     * A PropertyName object holds a property-name and it's data-type
     */
    public function getPropertyNames() {
        // {PropertyName(property-name, property-type),}
        $nameSet = array();
        foreach ($this->tree->getPropertyNames() as $name) {
            $nameSet[] = new Mobi_Mtld_DA_PropertyName(
                substr($name, 1),
                self::getPropertyTypeAsByte($name[0])
            );
        }
        return $nameSet;
    }

    /**
     * Get DeviceApi version.
     *
     * @return string DeviceApi version
     */
    public static function getApiVersion() {
       return self::API_VERSION;
    }

    /**
     * Get the device data (JSON file) version.
     *
     * @return string The data version
     */
    public function getDataVersion() {
       return $this->tree->getDataVersion();
    }

    /**
     * Get the device data creation (JSON file) timestamp.
     *
     * @return string The string data creation timestamp
     */
    public function getDataCreationTimestamp() {
        return $this->tree->getDataCreationTimestamp();
    }

    /**
     * Get the device data generation revision.
     *
     * @return int Data generation revision
     */
    public function getDataRevision() {
        return (int)trim(str_replace('$', ' ', substr($this->tree->getDataRevision(), 6)));
    }

    /**
     * @overload
     * Get the properties for a User-Agent merged with the client-side properties
     * (when provided by the DeviceAtlas client side component).
     * The DeviceAtlas client-side component (JavaScript library) collects the
     * client-side properties and puts them in a cookie.
     * The contents of this cookie must be passed to this method. The client properties
     * will over-ride any properties discovered from the main JSON data file.
     *
     * @param  string userAgent                 The User-Agent string
     * @param  string clientSideProperties=null The contents of the cookie containing the client side properties
     * @return Mobi_Mtld_DA_Properties A set of properties (Property objects) wrapped in a Properties object
     * @throws Mobi_Mtld_DA_Exception_ClientPropertiesException
     *
     * @overload
     * Get the properties for a set of HTTP headers merged with properties from the
     * client side component. The DeviceApi gets all the request HTTP headers and will
     * precisely detect the device and find property values by all usable headers.
     * Get the properties for a User-Agent merged with the client-side properties
     * (when provided by the DeviceAtlas client side component).
     * The DeviceAtlas client-side component (JavaScript library) collects the
     * client-side properties and puts them in a cookie.
     * The contents of this cookie must be passed to this method. The client properties
     * will over-ride any properties discovered from the main JSON data file.
     *
     * @param  array  headers                   Client's request HTTP headers
     * @param  string clientSideProperties=null The contents of the cookie containing the client side properties
     * @return Mobi_Mtld_DA_Properties A set of properties (Property objects) wrapped in a Properties object
     * @throws Mobi_Mtld_DA_Exception_ClientPropertiesException
     */
    public function getProperties($headers=null, $clientSideProperties=null) {

        if (!is_array($headers)) {
            return $this->getPropertiesFromUa($headers, $clientSideProperties);
        }

        // make header keys lower-cased with no underlines
        $headers = self::normaliseKeys($headers);

        // get user-agent-header-name list from the tree object
        // collect the stock-ua headers if any exists
        $stockUaHeaders = array();
        foreach ($this->tree->stockUaHeaders as $header) {
            if (isset($headers[$header])) {
                $stockUa = $headers[$header];
                $stockUaHeaders[] = $stockUa;
            }
        }

        // get the user-agent header
        if (isset($headers['user-agent'])) {
            $stockUaHeaders[] = $ua = $headers['user-agent'];
        } else {
            $ua = '';
        }

        // stockUaHeaders is used for device detection, ua is added to the end of this list
        $this->tree->putProperties($ua, $stockUaHeaders, $clientSideProperties);

        // add language and locale properties
        if (isset($headers[self::$LANGUAGE_HEADER]) && $this->config->getIncludeLangProps()) {
            $this->addLanguageProperties($headers[self::$LANGUAGE_HEADER]);
        }

        if ($this->tree->properties->isEmpty() && $this->config->getReturnNullWhenNoProperties()) {
            $properties = null;
        } else {
            $properties = $this->tree->properties;
        }

        return $properties;
    }

    /**
     * Get the properties for a User-Agent merged with the client-side properties
     * (when provided by the DeviceAtlas client side component).
     * The DeviceAtlas client-side component (JavaScript library) collects the
     * client-side properties and puts them in a cookie.
     * The contents of this cookie must be passed to this method. The client properties
     * will over-ride any properties discovered from the main JSON data file.
     *
     * @param  string userAgent                 The User-Agent string
     * @param  string clientSideProperties=null The contents of the cookie containing the client side properties
     * @return Mobi_Mtld_DA_Properties A set of properties (Property objects) wrapped in a Properties object
     * @throws Mobi_Mtld_DA_Exception_ClientPropertiesException
     */
    public function getPropertiesFromUa($userAgent, $clientSideProperties=null) {

        $this->tree->putProperties($userAgent, null, $clientSideProperties);

        if ($this->tree->properties->isEmpty() && $this->config->getReturnNullWhenNoProperties()) {
            return null;
        }

        return $this->tree->properties;
    }

    /**
     * Normalise the HTTP header keys, replaces "_" with "-" and removes any HTTP_ prefix
     */
    private static function normaliseKeys($keyVals) {
        $headers = array();

        foreach ($keyVals as $k => $v) {
            $k = str_replace('_', '-', strtolower($k));
            if (substr($k, 0, 5) === 'http-') {
                $k = substr($k, 5);
            }
            $headers[trim($k)] = $v;
        }

        return $headers;
    }

    /**
     * Convert tree property types to interface data types
     */
    private static function getPropertyTypeAsByte($typeChar) {
        if ($typeChar === 's') {
            return Mobi_Mtld_DA_DataType::STRING;
        }
        if ($typeChar === 'b') {
            return Mobi_Mtld_DA_DataType::BOOLEAN;
        }
        if ($typeChar === 'i') {
            return Mobi_Mtld_DA_DataType::INTEGER;
        }
        if ($typeChar === 'd') {
            return Mobi_Mtld_DA_DataType::DOUBLE;
        }
        return Mobi_Mtld_DA_DataType::UNKNOWN;
    }

    /**
     * Get the Accept-Language header and add language properties to the property list
     */
    private function addLanguageProperties($acceptLanguage) {
        $best  = '';
        $qBest = 0;
        // go through the header parts
        foreach (explode(',', $acceptLanguage) as $lang) {
            $lang = explode(';', $lang);
            // get q
            $q = 1;
            if (count($lang) > 1) {
                $s = trim($lang[1]);
                if (substr($s, 0, 2) === 'q=') {
                    $q = (float)trim(substr($s, 2));
                } else {
                    /* invalid data */
                    continue;
                }
            }
            // compare last best with current item, update if current item is better
            $locale = trim($lang[0]); // lang or locale string
            $len    = strlen($locale);

            if ($len === 2 || ($len > 4 && strpos($locale, '-') === 2) || $locale === '*') {
                if ($q > $qBest ||
                    ($q === $qBest && $len > 2 && $len > strlen($best) && substr($locale, 0, 2) === substr($best, 0, 2))) {

                    $best  = $locale;
                    $qBest = $q;
                    // if best item is found dont search more
                    if ($len === 5 && $q === 1) {
                        break;
                    }
                }
            }
        }

        // set lang properties
        if ($best !== '*') {
            $langLocale = explode('-', str_replace('_', '-', $best));
            $lang       = strtolower($langLocale[0]);
            $locale     = count($langLocale) === 2? ($lang.'-'.strtoupper($langLocale[1])): null;

            if ($lang) {
                $this->tree->properties->put(self::$PROPERTY_NAME_LANGUAGE, new Mobi_Mtld_DA_Property($lang, Mobi_Mtld_DA_DataType::STRING));
                if (strlen($locale) === 5) {
                    $this->tree->properties->put(self::$PROPERTY_NAME_LANGUAGE_LOCALE, new Mobi_Mtld_DA_Property($locale, Mobi_Mtld_DA_DataType::STRING));
                }
            }
        }
    }

    /**
     * Get the source properties fetch source to be used for debugging.
     *
     * @return string The value of one this constants: LOOKUP_SOURCE_TREE or LOOKUP_SOURCE_OPTIMIZED_TREE or LOOKUP_SOURCE_CACHE
     */
    public function getLookupSource() {
        return $this->tree->getLookupSource();
    }
}
