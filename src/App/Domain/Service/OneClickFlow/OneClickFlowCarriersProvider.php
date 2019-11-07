<?php


namespace App\Domain\Service\OneClickFlow;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowInterface;

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
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return OneClickFlowInterface|null
     */
    public function get(int $billingCarrierId, int $flowType): ?OneClickFlowInterface
    {
        foreach ($this->handlers as $oneClickFlowHandler) {
            if ($oneClickFlowHandler->canHandle($billingCarrierId, $flowType)) {
                return $oneClickFlowHandler;
            }
        }

        return null;
    }
}