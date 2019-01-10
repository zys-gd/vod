<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.11.18
 * Time: 12:03
 */

namespace SubscriptionBundle\Service\Action\Renew;


use Symfony\Component\Routing\RouterInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Common\RequestParametersProvider;

class RenewParametersProvider
{
    /**
     * @var RequestParametersProvider
     */
    private $parametersProvider;
    /**
     * @var string
     */
    private $host;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RenewParametersProvider constructor.
     * @param RequestParametersProvider $parametersProvider
     * @param string                    $host
     * @param RouterInterface           $router
     */
    public function __construct(
        RequestParametersProvider $parametersProvider,
        string $host,
        RouterInterface $router
    )
    {

        $this->parametersProvider = $parametersProvider;
        $this->host               = $host;
        $this->router             = $router;
    }


    public function provideParameters(Subscription $subscription): ProcessRequestParameters
    {

        $options = $this->parametersProvider->prepareRequestParameters($subscription);

        /** Override nullable variables for renew Command */
        $options->listener       = sprintf('http://%s%s', $this->host, $this->router->generate('talentica_subscription.listen'));
        $options->listenerWait   = sprintf('http://%s%s', $this->host, $this->router->generate('talentica_subscription.wait_listen'));
        $options->chargeProduct  = $subscription->getUuid();
        $options->chargeTier     = $subscription->getSubscriptionPack()->getTierId();
        $options->chargeStrategy = $subscription->getSubscriptionPack()->getRenewStrategyId();

        return $options;
    }
}