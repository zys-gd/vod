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
     *
     * @return OneClickFlowInterface|null
     */
    public function get($billingCarrierId): ?OneClickFlowInterface
    {
        foreach ($this->handlers as $oneClickFlowHandler) {
            if ($oneClickFlowHandler->canHandle($billingCarrierId)) {
                return $oneClickFlowHandler;
            }
        }

        return null;
    }
}