<?php


namespace Carriers\OrangeTNMM\Subscription;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use App\OneClickFlow\OneClickFlowInterface;
use App\OneClickFlow\OneClickFlowParameters;

class OrangeTNMMOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param CarrierInterface $carrier
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ORANGE_TUNISIA_MM;
    }

    public function getParameters(): array
    {
        return [
            OneClickFlowParameters::AVAILABLE_PARAMETERS['IS_LP_OFF']
        ];
    }
}