<?php

namespace DeviceDetectionBundle\Service;

require_once __DIR__ . "/../Resources/lib/Api/Device/DeviceApi.php";

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Monolog\Logger;
use DeviceDetectionBundle\Exceptions\LibraryException;
use Mobi_Mtld_DA_Device_DeviceApi;
use Mobi_Mtld_DA_Device_Config;
use Mobi_Mtld_DA_Properties;
use Exception;

class Device implements DeviceInterface
{
    /**
     * Path to the database file.
     */
    const DATABASE_FILE_PATH = __DIR__ . '/../../../var/devicedetection/database/db.json';

    /**
     * The optimizer breaks the data file into smaller parts and caches them on the disk.
     * The API will use the cached data instead and will only lead the data needed
     * for a certain lookup into the memory.
     *
     * Set to "false" to disable optimizer or use a path relative to the bundle to enable.
     */
    const DATABASE_OPTIMIZED_PATH = __DIR__ . '/../../../var/devicedetection/optimized';

    /**
     * After how much time should the cache expire, in seconds.
     */
    const CACHE_EXPIRE_AFTER = 86400; // one day

    /**
     * Internal constants used for mapping attributes.
     */
    const ATTR_DEVICE_ID       = 'id';
    const ATTR_DEVICE_VENDOR   = 'manufacturer';
    const ATTR_DEVICE_MODEL    = 'model';
    const ATTR_DEVICE_NAME     = 'marketingName';
    const ATTR_DISPLAY_WIDTH   = 'displayWidth';
    const ATTR_DISPLAY_HEIGHT  = 'displayHeight';
    const ATTR_DISPLAY_DENSITY = 'displayDensity';
    const ATTR_OS_TYPE         = 'osType';
    const ATTR_OS_NAME         = 'osName';
    const ATTR_OS_VERSION      = 'osVersion';

    /**
     * @var string The identifier which will be used to retrieve the device properties.
     */
    protected $_identifier;

    /**
     * @var AbstractAdapter The cache system.
     */
    protected $_cache;

    /**
     * @var Logger The logging system.
     */
    protected $_logger;

    /**
     * @var Mobi_Mtld_DA_Device_DeviceApi
     */
    protected $_api;

    /**
     * @inheritdoc
     */
    static public function getAvailableOsTypes($flip = false)
    {
        $osTypes = [
            static::OS_TYPE_SYMBIAN         => static::OS_NAME_SYMBIAN,
            static::OS_TYPE_ANDROID         => static::OS_NAME_ANDROID,
            static::OS_TYPE_IOS             => static::OS_NAME_IOS,
            static::OS_TYPE_RIM             => static::OS_NAME_RIM,
            static::OS_TYPE_WINDOWS_PHONE   => static::OS_NAME_WINDOWS_PHONE,
            static::OS_TYPE_BADA            => static::OS_NAME_BADA,
            static::OS_TYPE_WINDOWS_RT      => static::OS_NAME_WINDOWS_RT,
            static::OS_TYPE_WINDOWS_MOBILE  => static::OS_NAME_WINDOWS_MOBILE,
            static::OS_TYPE_WEB_OS          => static::OS_NAME_WEB_OS
        ];

        return $flip ? array_flip($osTypes) : $osTypes;
    }

    /**
     * @inheritdoc
     */
    public function __construct($identifier, $cache, $logger)
    {
        $this->_identifier = $identifier->get();
        $this->_cache      = $cache;
        $this->_logger     = $logger;

        if (!file_exists(static::DATABASE_FILE_PATH)) {

            throw new LibraryException('No device detection database found at the specified path');
        }

        try {

            $config = new Mobi_Mtld_DA_Device_Config();

            if (static::DATABASE_OPTIMIZED_PATH && file_exists(static::DATABASE_OPTIMIZED_PATH)) {

                $config->setUseTreeOptimizer(true);

                $config->setIgnoreDataFileChanges(true);

                $config->setOptimizerTempDir(static::DATABASE_OPTIMIZED_PATH);
            }

            $this->_api = new Mobi_Mtld_DA_Device_DeviceApi($config);

            $this->_api->loadDataFromFile(static::DATABASE_FILE_PATH);

        } catch (LibraryException $error) {

            $this->_logger->critical('Could not use the external library used for device detection', $error);

            throw new LibraryException($error->getMessage(), $error->getCode());
        }
    }

    /**
     * @inheritdoc
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = $identifier;
    }

    /**
     * Returns the DeviceAtlas device id
     *
     * @return int
     */
    public function getDeviceId()
    {
        $properties = $this->_query();
        return $properties[static::ATTR_DEVICE_ID];
    }

    /**
     * The company/organisation that provides a device, browser or other component to the market.
     * It can be a manufacturer, mobile operator or other organisation exclusively offering a product.
     *
     * @return string
     */
    public function getDeviceVendor()
    {
        $properties = $this->_query();
        return $properties[static::ATTR_DEVICE_VENDOR];
    }

    /**
     * The model name of a device, browser or some other component (e.g. Firefox - Windows).
     *
     * @return string
     */
    public function getDeviceModel()
    {
        $properties = $this->_query();
        return $properties[static::ATTR_DEVICE_MODEL];
    }

    /**
     * The marketing name for the device.
     *
     * @return string
     */
    public function getDeviceName()
    {
        $properties = $this->_query();
        return $properties[static::ATTR_DEVICE_NAME];
    }

    /**
     * @inheritdoc
     */
    public function getDisplayWidth()
    {
        $properties = $this->_query();

        return $properties[static::ATTR_DISPLAY_WIDTH];
    }

    /**
     * @inheritdoc
     */
    public function getDisplayHeight()
    {
        $properties = $this->_query();

        return $properties[static::ATTR_DISPLAY_HEIGHT];
    }

    /**
     * @inheritdoc
     */
    public function getDisplayDensity()
    {
        $properties = $this->_query();

        return $properties[static::ATTR_DISPLAY_DENSITY];
    }

    /**
     * @inheritdoc
     */
    public function getOsType()
    {
        $properties = $this->_query();

        return $properties[static::ATTR_OS_TYPE];
    }

    /**
     * @inheritdoc
     */
    public function getOsName()
    {
        $properties = $this->_query();

        return $properties[static::ATTR_OS_NAME];
    }

    /**
     * @inheritdoc
     */
    public function getOsVersion()
    {
        $properties = $this->_query();

        return $properties[static::ATTR_OS_VERSION];
    }

    /**
     * Queries the external resource for details regarding the identifier.
     * @param null|string $identifier
     * @return array
     * @throws LibraryException
     */
    protected function _query($identifier = null)
    {
        $identifier = is_null($identifier) ? $this->_identifier : $identifier;

        $key = 'devices.' . md5($identifier);

        $item = $this->_cache->getItem($key);

        if ($item->isHit()) {

            return $item->get();
        }

        $values = [];

        try {
            $properties = $this->_api->getProperties($identifier);

            // generic device properties
            $values[static::ATTR_DEVICE_ID    ] = $properties->id;
            $values[static::ATTR_DEVICE_VENDOR] = $properties->manufacturer;
            $values[static::ATTR_DEVICE_MODEL ] = $properties->model;
            $values[static::ATTR_DEVICE_NAME  ] = $properties->marketingName;

            // display properties
            $values[static::ATTR_DISPLAY_WIDTH]   = $properties->displayWidth;
            $values[static::ATTR_DISPLAY_HEIGHT]  = $properties->displayHeight;
            $values[static::ATTR_DISPLAY_DENSITY] = $properties->displayPpi;

            // os properties
            $values[static::ATTR_OS_TYPE]    = $this->_determineOsType($properties);
            $values[static::ATTR_OS_NAME]    = $properties->osName;
            $values[static::ATTR_OS_VERSION] = $properties->osVersion;
        } catch (Exception $error) {

            $this->_logger->critical('Could not query for properties', $error);

            throw new LibraryException($error->getMessage(), $error->getCode());
        }

        $item->set($values);
        $item->expiresAfter(static::CACHE_EXPIRE_AFTER);
        $this->_cache->save($item);

        return $values;
    }

    /**
     * Determines the operating system type, based on the properties object.
     * @param Mobi_Mtld_DA_Properties $properties
     * @return int
     */
    protected function _determineOsType(Mobi_Mtld_DA_Properties $properties)
    {
        if ($properties->osSymbian) {
            return static::OS_TYPE_SYMBIAN;
        }

        if ($properties->osAndroid) {
            return static::OS_TYPE_ANDROID;
        }

        if ($properties->osiOs) {
            return static::OS_TYPE_IOS;
        }

        if ($properties->osRim) {
            return static::OS_TYPE_RIM;
        }

        if ($properties->osWindowsPhone) {
            return static::OS_TYPE_WINDOWS_PHONE;
        }

        if ($properties->osBada) {
            return static::OS_TYPE_BADA;
        }

        if ($properties->osWindowsRt) {
            return static::OS_TYPE_WINDOWS_RT;
        }

        if ($properties->osWindowsMobile) {
            return static::OS_TYPE_WINDOWS_MOBILE;
        }

        if ($properties->osWebOs) {
            return static::OS_TYPE_WEB_OS;
        }

        return null;
    }
}