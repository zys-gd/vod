<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.03.19
 * Time: 16:31
 */

namespace SubscriptionBundle\Subscription\MassRenew;


use SubscriptionBundle\BillingFramework\BillingOptionsProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\RouteProvider;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;

class MassRenewParametersProvider
{
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var BillingOptionsProvider
     */
    private $billingOptionsProvider;


    /**
     * MassRenewParametersProvider constructor.
     * @param RouteProvider            $routeProvider
     * @param SubscriptionPackProvider $subscriptionPackProvider
     * @param BillingOptionsProvider   $billingOptionsProvider
     */
    public function __construct(
        RouteProvider $routeProvider,
        SubscriptionPackProvider $subscriptionPackProvider,
        BillingOptionsProvider $billingOptionsProvider
    )
    {
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->routeProvider            = $routeProvider;
        $this->billingOptionsProvider   = $billingOptionsProvider;
    }

    public function provideParameters(array $subscriptions): ProcessRequestParameters
    {
        $params = new ProcessRequestParameters();

        $params->listener = $this->routeProvider->getAbsoluteLinkForCallback('subscription.listen');
        $params->client   = $this->billingOptionsProvider->getClientId();


        $subs = [];
        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $user    = $subscription->getUser();
            $carrier = $user->getCarrier();

            $pack   = $this->subscriptionPackProvider->getActiveSubscriptionPackFromCarrier($carrier);
            $subs[] = [
                'client_id'       => $subscription->getUuid(),
                'client_user'     => $user->getIdentifier(),
                'charge_strategy' => $pack->getRenewStrategyId(),
                'charge_tier'     => $pack->getTierId(),
                'url_id'          => $user->getShortUrlId(),
            ];
        }

        $params->additionalData = ['subs' => $subs];

        return $params;
    }
}