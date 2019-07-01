<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.03.19
 * Time: 16:31
 */

namespace SubscriptionBundle\Service\Action\MassRenew;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use Symfony\Component\Routing\RouterInterface;

class MassRenewParametersProvider
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;


    /**
     * MassRenewParametersProvider constructor.
     * @param string                   $host
     * @param RouterInterface          $router
     * @param SubscriptionPackProvider $subscriptionPackProvider
     */
    public function __construct(string $host, RouterInterface $router, SubscriptionPackProvider $subscriptionPackProvider)
    {
        $this->host                     = $host;
        $this->router                   = $router;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
    }

    public function provideParameters(array $subscriptions): ProcessRequestParameters
    {
        $params = new ProcessRequestParameters();

        $params->listener = sprintf('http://%s%s', $this->host, $this->router->generate('subscription.listen'));

        $params->client = 'vod-store';


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