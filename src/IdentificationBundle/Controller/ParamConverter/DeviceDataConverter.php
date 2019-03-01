<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 13.01.2019
 * Time: 20:30
 */

namespace IdentificationBundle\Controller\ParamConverter;


use CountryCarrierDetectionBundle\Service\ConnectionTypeService;
use DeviceDetectionBundle\Service\Device;
use DeviceDetectionBundle\Service\DeviceInterface;
use IdentificationBundle\Identification\DTO\DeviceData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ISPDetectionConverter
 */
class DeviceDataConverter implements ParamConverterInterface
{
    /**
     * @var DeviceInterface
     */
    private $device;
    /**
     * @var ConnectionTypeService
     */
    private $connectionTypeService;

    /**
     * DeviceDataConverter constructor.
     * @param Device                $device
     * @param ConnectionTypeService $connectionTypeService
     */
    public function __construct(Device $device, ConnectionTypeService $connectionTypeService)
    {
        $this->device                = $device;
        $this->connectionTypeService = $connectionTypeService;
    }


    /**
     * Stores the object in the request.
     *
     * @param Request        $request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return void True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {

        $object = new DeviceData(
            $this->connectionTypeService->get() ?? '',
            '',
            $this->device->getDeviceVendor() ?? '',
            $this->device->getDeviceModel() ?? ''
        );


        $request->attributes->set($configuration->getName(), $object);
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() == DeviceData::class;
    }
}