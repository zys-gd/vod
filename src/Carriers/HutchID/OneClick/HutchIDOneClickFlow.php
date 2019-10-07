<?php


namespace Carriers\HutchID\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class HutchIDOneClickFlow implements  OneClickFlowInterface
{

    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::HUTCH3_INDONESIA_DOT;
    }

    public function getFlowType(): ?int
    {
        return  OneClickFlowParameters::CONFIRMATION_POP_UP;
    }
}