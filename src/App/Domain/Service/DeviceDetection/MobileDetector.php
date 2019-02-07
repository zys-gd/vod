<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.04.18
 * Time: 14:07
 */

namespace App\Domain\Service\DeviceDetection;


use Mobile_Detect;

class MobileDetector
{
    public function isAndroidOS()
    {
        $mobileDetect = new Mobile_Detect();

        return $mobileDetect->isAndroidOS();

    }
}