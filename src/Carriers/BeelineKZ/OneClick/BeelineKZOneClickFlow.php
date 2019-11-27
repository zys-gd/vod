<?php

namespace Carriers\BeelineKZ\OneClick;

use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

/**
 * Class BeelineKZOneClickFlow
 */
class BeelineKZOneClickFlow implements OneClickFlowInterface
{
    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId, int $flowType): bool
    {
        return $billingCarrierId === ID::BEELINE_KAZAKHSTAN_DOT && $flowType === $this->getFlowType();
    }

    /**
     * @return int
     */
    public function getFlowType(): int
    {
        return OneClickFlowParameters::CONFIRMATION_CLICK;
    }
}