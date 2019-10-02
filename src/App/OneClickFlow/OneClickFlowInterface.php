<?php


namespace App\OneClickFlow;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;

interface OneClickFlowInterface
{
    /**
     * @param CarrierInterface $carrier
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool;

    public function getParameters(): array;
}