<?php


namespace SubscriptionBundle\Carriers\OrangeTNMM\Subscribe;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasPassthroughFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class OrangeTNMMSubscriptionHandler implements SubscriptionHandlerInterface, HasPassthroughFlow
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::ORANGE_TUNISIA_MM;
    }
}