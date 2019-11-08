<?php

namespace IdentificationBundle\Twig;

use IdentificationBundle\Identification\Service\DeviceDataProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class UserDataExtension
 */
class UserDataExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var DeviceDataProvider
     */
    private $deviceDataProvider;

    /**
     * UserDataExtension constructor.
     *
     * @param RequestStack       $requestStack
     * @param DeviceDataProvider $deviceDataProvider
     */
    public function __construct(RequestStack $requestStack, DeviceDataProvider $deviceDataProvider)
    {
        $this->requestStack = $requestStack;
        $this->deviceDataProvider = $deviceDataProvider;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getForFakeCall', function () {
                $request = $this->requestStack->getCurrentRequest();
                $deviceData = $this->deviceDataProvider->get($request);

                return json_encode([
                    'user_device_manufacturer' => $deviceData->getDeviceManufacturer(),
                    'user_device_model' => $deviceData->getDeviceModel(),
                    'user_agent' => $request->headers->get('user-agent')
                ]);
            })
        ];
    }
}