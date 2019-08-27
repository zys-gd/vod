<?php


namespace SubscriptionBundle\Subscription\SubscribeBack\Handler;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Symfony\Component\HttpFoundation\Request;

interface SubscribeBackHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool;

    public function handleRequest(Request $request);
}