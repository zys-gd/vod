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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @param Device                $device
     * @param ConnectionTypeService $connectionTypeService
     */
    public function __construct(Device $device, ConnectionTypeService $connectionTypeService)
    {
        $this->device                = $device;
        $this->connectionTypeService = $connectionTypeService;
    }

    public function get(Request $request): DeviceData
    {
        $object = new DeviceData(
            $this->connectionTypeService->getByIp($request->getClientIp()) ?? '',
            '',
            $this->device->getDeviceVendor() ?? '',
            $this->device->getDeviceModel() ?? '',
            $request->getLocale()
        );

        return $object;

    }
}