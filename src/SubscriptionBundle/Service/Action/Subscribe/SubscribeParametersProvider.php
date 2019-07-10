<?php

namespace SubscriptionBundle\Service\Action\Subscribe;

use SubscriptionBundle\Service\ZeroCreditSubscriptionChecking;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Common\RequestParametersProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class SubscribeParametersProvider
{
    /**
     * @var RequestParametersProvider
     */
    private $parametersProvider;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;

    /**
     * SubscribeParametersProvider constructor
     *
     * @param RequestParametersProvider $parametersProvider
     * @param RequestStack $requestStack
     * @param RouterInterface $router
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     */
    public function __construct(
        RequestParametersProvider $parametersProvider,
        RequestStack $requestStack,
        RouterInterface $router,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
    ) {
        $this->parametersProvider = $parametersProvider;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
    }

    public function provideParameters(Subscription $subscription, array $additionalInfo): ProcessRequestParameters
    {
        $subscriptionPack = $subscription->getSubscriptionPack();
        $carrier = $subscription->getSubscriptionPack()->getCarrier();

        $parameters                         = $this->parametersProvider->prepareRequestParameters($subscription);
        $parameters->additionalData         = $additionalInfo;
        $parameters->chargeProduct          = $subscription->getUuid();
        $parameters->zeroCreditSubAvailable = $this->zeroCreditSubscriptionChecking->isAvailable($carrier);
        $parameters->chargeTier             = $subscriptionPack->getTierId();

        $parameters->chargeStrategy = $subscriptionPack->getBuyStrategyId();
        if ($request = $this->requestStack->getCurrentRequest()) {
            $parameters->redirectUrl = $request->get('location', $this->router->generate('subscription.wait_listen'));
        }
        return $parameters;
    }

}