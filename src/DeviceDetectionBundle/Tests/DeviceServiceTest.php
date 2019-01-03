<?php

namespace DeviceDetectionBundle\Tests;

use DeviceDetectionBundle\Service\Identifier;
use DeviceDetectionBundle\Service\Device;
use DeviceDetectionBundle\Service\DeviceInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Monolog\Logger;
use Psr\Cache\CacheItemInterface;

class DeviceServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Device
     */
    protected $_device;

    /**
     * Set up method.
     */
    protected function setUp()
    {
        $identifier = $this->createMock(Identifier::class);
        $cache      = $this->createMock(AbstractAdapter::class);
        $logger     = $this->createMock(Logger::class);
        $item       = $this->createMock(CacheItemInterface::class);

        $cache->method('getItem')
              ->will($this->returnValue($item));

        $this->_device = new Device($identifier, $cache, $logger);
    }

    /**
     * Tear down method.
     */
    protected function tearDown()
    {
        unset($this->_device);
    }

    /**
     * Data provider for user agents.
     */
    public function userAgentProvider()
    {
        $ua1 = 'Mozilla/5.0 (Linux; Android 4.2.2; GT-P5110 Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Safari/537.36';
        $ua2 = 'Mozilla/5.0 (Linux; Android 4.3; GT-I9300 Build/JSS15J) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Mobile Safari/537.36';
        $ua3 = 'Mozilla/5.0 (Linux; Android 5.1.1; C6603 Build/10.7.A.0.228) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.89 Mobile Safari/537.36';
        $ua4 = 'Mozilla/5.0 (Linux; Android 4.4.2; GT-I9195 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Mobile Safari/537.36';

        return [
            'SamsungGalaxyTab2' => [$ua1, 1280, 800, 149, DeviceInterface::OS_TYPE_ANDROID, 'Android', '4.2.2'],
            'SamsungGalaxyS3' => [$ua2, 720, 1280, 319, DeviceInterface::OS_TYPE_ANDROID, 'Android', '4.3'],
            'SonyXperiaZ' => [$ua3, 1080, 1920, 440, DeviceInterface::OS_TYPE_ANDROID, 'Android', '5.1.1'],
            'SamsungGalaxyS4Mini' => [$ua4, 540, 960, 256, DeviceInterface::OS_TYPE_ANDROID, 'Android', '4.4.2'],
        ];
    }

    /**
     * Tests the methods related to display.
     * @dataProvider userAgentProvider
     * @param $ua
     * @param $width
     * @param $height
     * @param $ppi
     * @param $osType
     * @param $osName
     * @param $osVersion
     */
    public function testDisplay($ua, $width, $height, $ppi, $osType, $osName, $osVersion)
    {
        $this->_device->setIdentifier($ua);

        $this->assertEquals($width, $this->_device->getDisplayWidth());
        $this->assertEquals($height, $this->_device->getDisplayHeight());
        $this->assertEquals($ppi, $this->_device->getDisplayDensity());
    }

    /**
     * Tests the methods related to os.
     * @dataProvider userAgentProvider
     * @param $ua
     * @param $width
     * @param $height
     * @param $ppi
     * @param $osType
     * @param $osName
     * @param $osVersion
     */
    public function testOs($ua, $width, $height, $ppi, $osType, $osName, $osVersion)
    {
        $this->_device->setIdentifier($ua);

        $this->assertEquals($osType, $this->_device->getOsType());
        $this->assertEquals($osName, $this->_device->getOsName());
        $this->assertEquals($osVersion, $this->_device->getOsVersion());
    }
}