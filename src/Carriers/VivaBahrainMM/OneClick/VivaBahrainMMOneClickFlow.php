<?php

namespace Carriers\VivaBahrainMM\OneClick;

use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

/**
 * Class VivaBahrainMMOneClickFlow
 */
class VivaBahrainMMOneClickFlow implements OneClickFlowInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::VIVA_BAHRAIN_MM;
    }

    /**
     * @return int|null
     */
    public function getFlowType(): ?int
    {
        return OneClickFlowParameters::LP_OFF;
    }
}