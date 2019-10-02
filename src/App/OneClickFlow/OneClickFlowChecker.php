<?php


namespace App\OneClickFlow;


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
     * @param CarrierInterface $carrier
     * @param int $parameter
     * @return bool
     */
    public function check(CarrierInterface $carrier, int $parameter): bool
    {
        /** @var OneClickFlowInterface $carrierHandler */
        $carrierOneClickFlowHandler = $this->oneClickFlowCarriersProvider->get($carrier);
        $carrierOneClickFlowParameters = $carrierOneClickFlowHandler->getParameters();

        return in_array($parameter, $carrierOneClickFlowParameters);
    }
}

