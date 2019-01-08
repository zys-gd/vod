<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:53
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Service\Action\Identification\Handler\IdentificationHandlerInterface;

class MobilinkPKIdentificationHandler implements IdentificationHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
    }
}