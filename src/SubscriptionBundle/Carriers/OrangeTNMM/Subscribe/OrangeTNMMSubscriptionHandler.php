<?php


namespace SubscriptionBundle\Carriers\OrangeTNMM\Subscribe;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasPassthroughFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;

class OrangeTNMMSubscriptionHandler implements SubscriptionHandlerInterface, HasPassthroughFlow
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ORANGE_TUNISIA_MM;
    }
}