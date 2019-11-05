<?php


namespace Carriers\ZongPK\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class ZongPKOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId, int $flowType): bool
    {
        return $billingCarrierId === ID::ZONG_PAKISTAN && $flowType === $this->getFlowType();
    }

    /**
     * @return int|null
     */
    public function getFlowType(): int
    {
        return OneClickFlowParameters::CONFIRMATION_CLICK;
    }
}