<?php


namespace Carriers\HutchID\Subscription;


use IdentificationBundle\BillingFramework\ID;
use App\OneClickFlow\OneClickFlowInterface;
use App\OneClickFlow\OneClickFlowParameters;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;

class HutchIDOneClickFlow implements  OneClickFlowInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::HUTCH3_INDONESIA_DOT;
    }

    public function getParameters(): array
    {
        return [
            OneClickFlowParameters::AVAILABLE_PARAMETERS['IS_CONFIRMATION_POP_UP']
        ];
    }
}