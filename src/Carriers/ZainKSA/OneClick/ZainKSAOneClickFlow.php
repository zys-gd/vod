<?php


namespace Carriers\ZainKSA\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class ZainKSAOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::ZAIN_SAUDI_ARABIA;
    }

    public function getFlowType(): ?int
    {
        return  OneClickFlowParameters::CONFIRMATION_CLICK;
    }
}