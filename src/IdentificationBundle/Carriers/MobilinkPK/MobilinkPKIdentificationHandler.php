<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:53
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Service\Action\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Service\Action\Identification\Handler\IdentificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class MobilinkPKIdentificationHandler implements IdentificationHandlerInterface, HasCommonFlow
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