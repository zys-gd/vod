<?php


namespace App\Domain\Service\OneClickFlow;

interface OneClickFlowInterface
{
    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId, int $flowType): bool;

    /**
     * @return int
     */
    public function getFlowType(): int;
}