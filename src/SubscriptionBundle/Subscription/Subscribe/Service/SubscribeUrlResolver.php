<?php


namespace SubscriptionBundle\Subscription\Subscribe\Service;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
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
     * @param null $identificationToken
     *
     * @return string
     */
    public function getSubscribeRoute(CarrierInterface $carrier, $identificationToken = null): string
    {
        $subscriber = $this->handlerProvider->getSubscriber($carrier);

        // paste needed interface and uncomment
         if($subscriber instanceof HasConsentPageFlow || $subscriber instanceof HasCommonConsentPageFlow) {
             return $this->route->generate('subscription.consent_page_subscribe');
         }

         if ($identificationToken) {
             return $this->route->generate('subscription.subscribe');
         }

        return $this->route->generate('identify_and_subscribe');
    }
}