<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.01.19
 * Time: 13:46
 */

namespace IdentificationBundle\Carriers\AirtelIN;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Handler\HasConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class AirtelINIdentificationHandler implements HasConsentPageFlow, IdentificationHandlerInterface
{

    public function onProcess(Request $request): void
    {
        // TODO: Implement onProcess() method.
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() == ConstBillingCarrierId::AIRTEL_INDIA;
    }
}