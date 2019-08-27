<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.01.19
 * Time: 13:46
 */

namespace IdentificationBundle\Carriers\AirtelIN;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AirtelINIdentificationHandler implements HasCommonConsentPageFlow, HasConsentPageFlow, IdentificationHandlerInterface
{
    /**
     * @param Request $request
     * @param CarrierInterface $carrier
     * @param string $token
     *
     * @return RedirectResponse
     */
    public function onProcess(Request $request, CarrierInterface $carrier, string $token): RedirectResponse
    {
        // TODO: Implement onProcess() method.
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() == ID::AIRTEL_INDIA;
    }

    /**
     * @param Request $request
     * @param CarrierInterface $carrier
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request, CarrierInterface $carrier): array
    {
        // TODO: Implement getAdditionalIdentificationParams() method.
    }
}