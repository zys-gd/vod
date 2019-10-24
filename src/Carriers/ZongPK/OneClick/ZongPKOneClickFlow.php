<?php


namespace Carriers\ZongPK\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class ZongPKOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::ZONG_PAKISTAN;
    }

    /**
     * @return int|null
     */
    public function getFlowType(): ?int
    {
        return OneClickFlowParameters::CONFIRMATION_CLICK;
    }
}