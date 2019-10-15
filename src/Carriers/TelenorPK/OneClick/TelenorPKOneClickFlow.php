<?php


namespace Carriers\TelenorPK\OneClick;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;

class TelenorPKOneClickFlow implements OneClickFlowInterface
{

    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::TELENOR_PAKISTAN_DOT;
    }

    public function getFlowType(): ?int
    {
        return  OneClickFlowParameters::CONFIRMATION_CLICK;
    }
}