<?php


namespace Carriers\OrangeTNMM\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class OrangeTNMMOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::ORANGE_TUNISIA_MM;
    }

    /**
     * @return int|null
     */
    public function getFlowType(): ?int
    {
        return OneClickFlowParameters::LP_OFF;
    }
}