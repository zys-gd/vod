<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.09.19
 * Time: 10:45
 */

namespace App\Twig;


use App\Domain\Service\DeviceDetection\MobileDetector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DeviceOSExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('isAndroidOS', function () {
                $mobileDetector = new MobileDetector();

                return $mobileDetector->isAndroidOS();
            })
        ];
    }


}