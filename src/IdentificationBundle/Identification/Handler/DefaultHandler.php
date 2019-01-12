<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:40
 */

namespace IdentificationBundle\Identification\Handler;


use IdentificationBundle\Entity\CarrierInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultHandler implements IdentificationHandlerInterface, HasCommonFlow
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
    }

    public function getAdditionalIdentificationParams(Request $request): array
    {
        return [];
    }
}