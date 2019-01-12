<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:53
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\CommonFlow\HasCustomPixelIdent;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Request;

class MobilinkPKIdentificationHandler implements
    IdentificationHandlerInterface,
    HasCommonFlow,
    HasCustomPixelIdent
{
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === 338;
    }

    public function getAdditionalIdentificationParams(Request $request): array
    {
        return [];
    }

    public function onConfirm(ProcessResult $processResult): void
    {
        // TODO: Implement onConfirm() method.
    }
}