<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.03.19
 * Time: 14:31
 */

namespace IdentificationBundle\Identification\DTO;


class DeviceData
{
    /**
     * @var string
     */
    private $connectionType;
    /**
     * @var string
     */
    private $identificationUrl;
    /**
     * @var string
     */
    private $deviceManufacturer;
    /**
     * @var string
     */
    private $deviceModel;

    /**
     * DeviceData constructor.
     * @param string $connectionType
     * @param string $identificationUrl
     * @param string $deviceManufacturer
     * @param string $deviceModel
     */
    public function __construct(string $connectionType, string $identificationUrl, string $deviceManufacturer, string $deviceModel)
    {
        $this->connectionType     = $connectionType;
        $this->identificationUrl  = $identificationUrl;
        $this->deviceManufacturer = $deviceManufacturer;
        $this->deviceModel        = $deviceModel;
    }


    /**
     * @return string
     */
    public function getConnectionType(): string
    {
        return $this->connectionType;
    }

    /**
     * @return string
     */
    public function getIdentificationUrl(): string
    {
        return $this->identificationUrl;
    }

    /**
     * @return string
     */
    public function getDeviceManufacturer(): string
    {
        return $this->deviceManufacturer;
    }

    /**
     * @return string
     */
    public function getDeviceModel(): string
    {
        return $this->deviceModel;
    }




}