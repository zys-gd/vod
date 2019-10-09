<?php


namespace SubscriptionBundle\Subscription\Subscribe\Service;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\Process\PassthroughProcess;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use IdentificationBundle\Identification\Service\PassthroughRequestPreparer;
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use Symfony\Component\HttpFoundation\Request;
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
    private $subscriptionHandlerProvider;
    /**
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;
    /**
     * @var PassthroughRequestPreparer
     */
    private $passthroughRequestPreparer;
    /**
     * @var PassthroughProcess
     */
    private $passthroughProcess;

    /**
     * SubscribeUrlResolver constructor.
     *
     * @param RouterInterface               $route
     * @param SubscriptionHandlerProvider   $handlerProvider
     * @param IdentificationHandlerProvider $identificationHandlerProvider
     * @param PassthroughRequestPreparer    $passthroughRequestPreparer
     * @param PassthroughProcess            $passthroughProcess
     */
    public function __construct(
        RouterInterface $route,
        SubscriptionHandlerProvider $handlerProvider,
        IdentificationHandlerProvider $identificationHandlerProvider,
        PassthroughRequestPreparer $passthroughRequestPreparer,
        PassthroughProcess $passthroughProcess
    )
    {
        $this->route                         = $route;
        $this->subscriptionHandlerProvider   = $handlerProvider;
        $this->identificationHandlerProvider = $identificationHandlerProvider;
        $this->passthroughRequestPreparer    = $passthroughRequestPreparer;
        $this->passthroughProcess            = $passthroughProcess;
    }

    /**
     * @param Request          $request
     * @param CarrierInterface $carrier
     * @param null             $identificationToken
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSubscribeRoute(Request $request, CarrierInterface $carrier, $identificationToken = null): string
    {
        /** @var SubscriptionHandlerInterface $subscriber */
        $subscriber = $this->subscriptionHandlerProvider->getSubscriber($carrier);

        /** @var IdentificationHandlerInterface $identifier */
        $identifier = $this->identificationHandlerProvider->get($carrier);

        if ($identifier instanceof HasPassthroughFlow) {
            $parameters      = $this->passthroughRequestPreparer->getProcessRequestParameters($request);
            $passthroughLink = $this->passthroughProcess->runPassthrough($parameters);
            return $passthroughLink;
        }

        if ($identificationToken) {
            if ($identifier instanceof HasCommonConsentPageFlow) {
                return $this->route->generate('subscription.consent_page_subscribe');
            }
            return $this->route->generate('subscription.subscribe');
        }

        return $this->route->generate('identify_and_subscribe');
    }
}