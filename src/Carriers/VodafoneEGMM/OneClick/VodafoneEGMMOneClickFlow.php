<?php


namespace Carriers\VodafoneEGMM\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class VodafoneEGMMOneClickFlow implements OneClickFlowInterface
{
    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId, int $flowType): bool
    {
        return $billingCarrierId === ID::VODAFONE_EGYPT_MM && $flowType === $this->getFlowType();
    }

    public function getFlowType(): int
    {
        return  OneClickFlowParameters::LP_OFF;
    }
}