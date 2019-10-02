<?php


namespace App\OneClickFlow;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use App\OneClickFlow\OneClickFlowInterface;

class OneClickFlowCarriersProvider
{
    /**
     * @var OneClickFlowInterface[]
     */
    private $handlers = [];

    /**
     * @param OneClickFlowInterface $oneClickFlowHandler
     */
    public function addHandler(OneClickFlowInterface $oneClickFlowHandler): void
    {
        $this->handlers[] = $oneClickFlowHandler;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return OneClickFlowInterface|null
     */
    public function get(CarrierInterface $carrier): ?OneClickFlowInterface
    {
        foreach ($this->handlers as $oneClickFlowHandler) {
            if ($oneClickFlowHandler->canHandle($carrier)) {
                return $oneClickFlowHandler;
            }
        }

        return null;
    }
}