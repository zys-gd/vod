<?php


namespace SubscriptionBundle\Service\Action\SubscribeBack\Handler;


use IdentificationBundle\Entity\CarrierInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultHandler implements SubscribeBackHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
    }

    public function handleRequest(Request $request)
    {
        // TODO: Implement handleRequest() method.
    }
}