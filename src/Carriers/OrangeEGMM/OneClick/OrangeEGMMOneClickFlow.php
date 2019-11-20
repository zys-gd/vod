<?php


namespace Carriers\OrangeEGMM\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class OrangeEGMMOneClickFlow implements OneClickFlowInterface
{
    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId, int $flowType): bool
    {
        return $billingCarrierId === ID::ORANGE_EG_MM && $flowType === $this->getFlowType();
    }

    public function getFlowType(): int
    {
        return  OneClickFlowParameters::LP_OFF;
    }
}