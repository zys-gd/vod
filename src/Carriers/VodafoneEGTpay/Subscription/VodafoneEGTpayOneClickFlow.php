<?php


namespace Carriers\VodafoneEGTpay\Subscription;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use App\OneClickFlow\OneClickFlowInterface;
use App\OneClickFlow\OneClickFlowParameters;

class VodafoneEGTpayOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param CarrierInterface $carrier
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::VODAFONE_EGYPT_TPAY;
    }

    public function getParameters(): array
    {
        return [
            OneClickFlowParameters::AVAILABLE_PARAMETERS['IS_LP_OFF']
        ];
    }
}