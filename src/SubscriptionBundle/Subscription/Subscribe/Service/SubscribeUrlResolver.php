<?php


namespace SubscriptionBundle\Subscription\Subscribe\Service;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use Symfony\Component\Routing\RouterInterface;

class SubscribeUrlResolver
{
    /**
     * @var RouterInterface
     */
    private $route;
    /**
     * @var SubscriptionHandlerProvider
     */
    private $handlerProvider;

    /**
     * SubscribeUrlResolver constructor.
     *
     * @param RouterInterface             $route
     * @param SubscriptionHandlerProvider $handlerProvider
     */
    public function __construct(RouterInterface $route, SubscriptionHandlerProvider $handlerProvider)
    {
        $this->route           = $route;
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return string
     */
    public function getSubscribeRoute(CarrierInterface $carrier): string
    {
        $subscriber = $this->handlerProvider->getSubscriber($carrier);

        // paste needed interface and uncomment
        // if($subscriber instanceof HasConsentPageFlow) {
        //     return $this->route->generate('subscription.consent_page_subscribe');
        // }

        return $this->route->generate('identify_and_subscribe');
    }
}