<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 17:05
 */

namespace IdentificationBundle\Identification\Service;


use IdentificationBundle\Entity\CarrierInterface;

class ISPResolver
{
    public function isISPMatches(string $carrierISP, CarrierInterface $carrier): bool
    {
        $maxMindIsp = explode('|', $carrierISP);
        $ispArray   = explode('|', $carrier->getIsp());

        foreach ($ispArray as $isp) {
            if (in_array($isp, $maxMindIsp)) {
                return true;
            }
        }

        return false;;
    }
}