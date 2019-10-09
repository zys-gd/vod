<?php


namespace Carriers\OrangeEGTpay\OneClick;


use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;

class OrangeEGTpayOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::ORANGE_EGYPT_TPAY;
    }

    /**
     * @return int|null
     */
    public function getFlowType(): ?int
    {
        return OneClickFlowParameters::LP_OFF;
    }
}