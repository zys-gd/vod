<?php


namespace SubscriptionBundle\Service\Action\SubscribeBack\Handler;


use IdentificationBundle\Entity\CarrierInterface;
use Symfony\Component\HttpFoundation\Request;

interface SubscribeBackHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool;

    public function handleRequest(Request $request);
}