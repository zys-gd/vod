<?php


namespace App\Domain\Service\OneClickFlow;

interface OneClickFlowInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool;

    /**
     * @return int|null
     */
    public function getFlowType(): ?int;
}