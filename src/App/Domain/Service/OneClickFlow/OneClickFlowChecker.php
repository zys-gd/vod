<?php


namespace App\Domain\Service\OneClickFlow;


use App\Domain\Entity\Carrier;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;

class OneClickFlowChecker
{
    /**
     * @var OneClickFlowCarriersProvider
     */
    private $oneClickFlowCarriersProvider;

    public function __construct(OneClickFlowCarriersProvider $oneClickFlowCarriersProvider)
    {
        $this->oneClickFlowCarriersProvider = $oneClickFlowCarriersProvider;
    }

    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function check(int $billingCarrierId, int $flowType): bool
    {
        /** @var OneClickFlowInterface $carrierHandler */
        $carrierOneClickFlowHandler = $this->oneClickFlowCarriersProvider->get($billingCarrierId);

        if (!$carrierOneClickFlowHandler) {
            return false;
        }

        $carrierOneClickFlowType = $carrierOneClickFlowHandler->getFlowType();

        return $carrierOneClickFlowType === $flowType;
    }
}

