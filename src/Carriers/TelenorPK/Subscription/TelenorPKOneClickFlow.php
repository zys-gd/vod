<?php


namespace Carriers\TelenorPK\Subscription;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use App\OneClickFlow\OneClickFlowInterface;
use App\OneClickFlow\OneClickFlowParameters;

class TelenorPKOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param CarrierInterface $carrier
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TELENOR_PAKISTAN_DOT;
    }

    public function getParameters(): array
    {
        return [
            OneClickFlowParameters::AVAILABLE_PARAMETERS['IS_CONFIRMATION_CLICK']
        ];
    }
}