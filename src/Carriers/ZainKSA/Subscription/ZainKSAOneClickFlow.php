<?php


namespace Carriers\ZainKSA\Subscription;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use App\OneClickFlow\OneClickFlowInterface;
use App\OneClickFlow\OneClickFlowParameters;

class ZainKSAOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param CarrierInterface $carrier
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ZAIN_SAUDI_ARABIA;
    }

    public function getParameters(): array
    {
        return [
            OneClickFlowParameters::AVAILABLE_PARAMETERS['IS_CONFIRMATION_CLICK']
        ];
    }
}