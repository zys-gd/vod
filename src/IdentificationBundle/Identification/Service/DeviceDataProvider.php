<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.03.19
 * Time: 18:56
 */

namespace IdentificationBundle\Identification\Service;


use CountryCarrierDetectionBundle\Service\ConnectionTypeService;
use DeviceDetectionBundle\Service\Device;
use IdentificationBundle\Identification\DTO\DeviceData;

class DeviceDataProvider
{
    /**
     * @var Device
     */
    private $device;
    /**
     * @var ConnectionTypeService
     */
    private $connectionTypeService;


    /**
     * DeviceDataProvider constructor.
     */
    public function __construct(Device $device, ConnectionTypeService $connectionTypeService)
    {
        $this->device                = $device;
        $this->connectionTypeService = $connectionTypeService;
    }

    public function get(): DeviceData
    {

        $object = new DeviceData(
            $this->connectionTypeService->get() ?? '',
            '',
            $this->device->getDeviceVendor() ?? '',
            $this->device->getDeviceModel() ?? ''
        );

        return $object;

    }
}