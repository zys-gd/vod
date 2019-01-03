<?php

namespace DeviceDetectionBundle\Service;

use DeviceDetectionBundle\Service\Identifier;
use Monolog\Logger;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

interface DeviceInterface
{
    /**
     * Constants that define the operating system types.
     */
    const OS_TYPE_SYMBIAN        = 1;
    const OS_TYPE_ANDROID        = 2;
    const OS_TYPE_IOS            = 3;
    const OS_TYPE_RIM            = 4;
    const OS_TYPE_WINDOWS_PHONE  = 5;
    const OS_TYPE_BADA           = 6;
    const OS_TYPE_WINDOWS_RT     = 7;
    const OS_TYPE_WINDOWS_MOBILE = 8;
    const OS_TYPE_WEB_OS         = 9;

    /**
     * Constants that define the operating system names.
     */
    const OS_NAME_SYMBIAN        = 'Symbian';
    const OS_NAME_ANDROID        = 'Android';
    const OS_NAME_IOS            = 'iOS';
    const OS_NAME_RIM            = 'Rim';
    const OS_NAME_WINDOWS_PHONE  = 'Windows Phone';
    const OS_NAME_BADA           = 'Bada';
    const OS_NAME_WINDOWS_RT     = 'Windows RT';
    const OS_NAME_WINDOWS_MOBILE = 'Windows Mobile';
    const OS_NAME_WEB_OS         = 'WebOS';

    /**
     * Returns a list with supported OSes having the "type" as key and "name" as value.
     * @param bool $flip
     * @return array
     */
    static function getAvailableOsTypes($flip = false);

    /**
     * DeviceInterface constructor.
     * @param Identifier $identifier
     * @param AbstractAdapter $cache
     * @param Logger $logger
     */
    public function __construct($identifier, $cache, $logger);

    /**
     * Sets the identifier which will be used to retrieve the properties.
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * Returns the display width in pixels.
     * @return int
     */
    public function getDisplayWidth();

    /**
     * Returns the display height in pixels.
     * @return int
     */
    public function getDisplayHeight();

    /**
     * Returns the display density (ppi)
     * @return int
     */
    public function getDisplayDensity();

    /**
     * Returns the operating system type.
     * @return int
     */
    public function getOsType();

    /**
     * Returns the operating system name (Android, iOS, Windows, etc.).
     * @return string
     */
    public function getOsName();

    /**
     * Returns the version of the operating system.
     * @return string
     */
    public function getOsVersion();
}