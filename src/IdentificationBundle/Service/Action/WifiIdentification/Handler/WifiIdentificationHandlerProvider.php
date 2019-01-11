<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:56
 */

namespace IdentificationBundle\Service\Action\WifiIdentification\Handler;


use IdentificationBundle\Entity\CarrierInterface;

class WifiIdentificationHandlerProvider
{

    /**
     * @var WifiIdentificationHandlerInterface[]
     */
    private $handlers = [];


    public function addHandler(WifiIdentificationHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function get(CarrierInterface $carrier): WifiIdentificationHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($carrier)) {
                return $handler;
            }
        }

        throw new \InvalidArgumentException('Carrier is not support wifi flow');
    }
}