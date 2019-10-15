<?php


namespace Carriers\MobilinkPK\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class MobilinkPKOneClickFlow implements OneClickFlowInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::MOBILINK_PAKISTAN;
    }

    public function getFlowType(): ?int
    {
        return  OneClickFlowParameters::CONFIRMATION_CLICK;
    }
}