<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 10:25
 */

namespace SubscriptionBundle\Subscription\Common;


use SubscriptionBundle\BillingFramework\BillingOptionsProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class RequestParametersProvider
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var BillingOptionsProvider
     */
    private $billingOptionsProvider;


    /**
     * BillingFrameworkHelperService constructor.
     * @param RequestStack           $requestStack
     * @param RouterInterface        $router
     * @param RouteProvider          $routeProvider
     * @param BillingOptionsProvider $billingOptionsProvider
     */
    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router,
        RouteProvider $routeProvider,
        BillingOptionsProvider $billingOptionsProvider
    )
    {

        $this->requestStack           = $requestStack;
        $this->router                 = $router;
        $this->routeProvider          = $routeProvider;
        $this->billingOptionsProvider = $billingOptionsProvider;
    }

    /**
     * @param Subscription $subscription
     * @return ProcessRequestParameters
     */
    public function prepareRequestParameters(Subscription $subscription): ProcessRequestParameters
    {

        $process               = new ProcessRequestParameters();
        $process->listener     = $this->routeProvider->getAbsoluteLinkForCallback('subscription.listen');
        $process->client       = $this->billingOptionsProvider->getClientId();
        $process->listenerWait = $this->routeProvider->getAbsoluteLinkForCallback('subscription.listen');

        // Not sure if its acceptable to have Request parsing here.
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $process->redirectUrl = $request->get('location', $request->getSchemeAndHttpHost());
        } else {
            $process->redirectUrl = $this->router->generate("homepage");
        }

        $process->clientId = $subscription->getUuid();
        $process->carrier  = $subscription->getSubscriptionPack()->getCarrier()->getBillingCarrierId();
        $process->userIp   = $subscription->getUser()->getIp();

        // The request headers of the end user.
        $currentUserRequestHeaders = '';
        if ($request && is_array($request->headers->all())) {
            foreach ($request->headers->all() as $key => $value) {

                if ($key == 'cookie') {
                    continue;
                }

                $currentUserRequestHeaders .= "{$key}: {$value[0]}\r\n";
            }
        }
        $process->userHeaders = $currentUserRequestHeaders;
        $process->clientUser  = $subscription->getUser()->getIdentifier();

        return $process;
    }
}