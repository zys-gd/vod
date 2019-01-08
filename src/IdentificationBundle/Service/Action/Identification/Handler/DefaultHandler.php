<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:40
 */

namespace IdentificationBundle\Service\Action\Identification\Handler;


use IdentificationBundle\Entity\CarrierInterface;

class DefaultHandler implements IdentificationHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
    }
}