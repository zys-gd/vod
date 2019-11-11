<?php


namespace Carriers\MobilinkPK\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class MobilinkPKOneClickFlow implements OneClickFlowInterface
{
    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId, int $flowType): bool
    {
        return false; //$billingCarrierId === ID::MOBILINK_PAKISTAN && $flowType === $this->getFlowType();
    }

    public function getFlowType(): int
    {
        return  OneClickFlowParameters::CONFIRMATION_CLICK;
    }
}