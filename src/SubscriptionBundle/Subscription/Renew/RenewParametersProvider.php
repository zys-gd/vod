<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.11.18
 * Time: 12:03
 */

namespace SubscriptionBundle\Subscription\Renew;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\RequestParametersProvider;
use SubscriptionBundle\Subscription\Common\RouteProvider;
use Symfony\Component\Routing\RouterInterface;

class RenewParametersProvider
{
    /**
     * @var RequestParametersProvider
     */
    private $parametersProvider;
    /**
     * @var \SubscriptionBundle\Subscription\Common\RouteProvider
     */
    private $routeProvider;

    /**
     * RenewParametersProvider constructor.
     * @param RequestParametersProvider $parametersProvider
     * @param RouteProvider             $routeProvider
     */
    public function __construct(
        RequestParametersProvider $parametersProvider,
        RouteProvider $routeProvider
    )
    {

        $this->parametersProvider = $parametersProvider;
        $this->routeProvider      = $routeProvider;
    }


    public function provideParameters(Subscription $subscription): ProcessRequestParameters
    {

        $options = $this->parametersProvider->prepareRequestParameters($subscription);

        /** Override nullable variables for renew Command */
        $options->listener       = $this->routeProvider->getAbsoluteLinkForCallback('subscription.listen');
        $options->listenerWait   = $this->routeProvider->getAbsoluteLinkForCallback('subscription.listen');
        $options->chargeProduct  = $subscription->getUuid();
        $options->chargeTier     = $subscription->getSubscriptionPack()->getTierId();
        $options->chargeStrategy = $subscription->getSubscriptionPack()->getRenewStrategyId();

        return $options;
    }
}