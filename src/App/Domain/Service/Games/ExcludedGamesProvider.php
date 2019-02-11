<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 11-02-19
 * Time: 17:40
 */

namespace App\Domain\Service\Games;


use DeviceDetectionBundle\Service\Device;

class ExcludedGamesProvider
{
    private $excludedGames = array(
        'SM-G920A' => array(
            69
        ),
        'G920A' => array(
            69
        ),
        'SM-G920F' => array(
            69
        ),
        'G920F' => array(
            69
        ),
        'SM-G920S' => array(
            69
        ),
        'G920S' => array(
            69
        ),
        'SM-G920T' => array(
            69
        ),
        'G920T' => array(
            69
        ),
        'SM-G9200' => array(
            69
        ),
        'G9200' => array(
            69
        ),
        'SM-G9208' => array(
            69
        ),
        'G9208' => array(
            69
        ),

        'RM-1096' => array(
            6, 18
        ),
        'D724' => array(
            6, 18
        )
    );

    /**
     * @var Device
     */
    private $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function get(array $manuallyExcluded = [])
    {
        $deviceModel = $this->device->getDeviceModel();
        if (null === $deviceModel) {
            return $manuallyExcluded;
        }
        if (array_key_exists($deviceModel, $this->excludedGames)) {
            return array_unique(array_merge($this->excludedGames[$deviceModel], $manuallyExcluded));
        }

        return $manuallyExcluded;
    }
}