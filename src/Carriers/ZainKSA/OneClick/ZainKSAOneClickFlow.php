<?php


namespace Carriers\ZainKSA\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class ZainKSAOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId, int $flowType): bool
    {
        return $billingCarrierId === ID::ZAIN_SAUDI_ARABIA && $flowType === $this->getFlowType();
    }

    public function getFlowType(): int
    {
        return  OneClickFlowParameters::CONFIRMATION_CLICK;
    }
}