<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 10:25
 */

namespace SubscriptionBundle\Service\Action\Common;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Entity\Subscription;

class RequestParametersProvider
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var SessionInterface
     */
    private $session;


    /**
     * BillingFrameworkHelperService constructor.
     * @param RequestStack                                                      $requestStack
     * @param EventDispatcherInterface                                          $eventDispatcher
     * @param RouterInterface                                                   $router
     * @param SessionInterface                                                  $session
     * @param \SubscriptionBundle\BillingFramework\Process\API\Client $apiClient
     */
    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router,
        SessionInterface $session
    )
    {

        $this->requestStack    = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->router          = $router;
        $this->session         = $session;
    }

    /**
     * @param Subscription $subscription
     * @return ProcessRequestParameters
     */
    public function prepareRequestParameters(Subscription $subscription): ProcessRequestParameters
    {

        $process               = new ProcessRequestParameters();
        $process->listener     = $this->router->generate('subscription.listen', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $process->client       = 'vod-store';
        $process->listenerWait = $this->router->generate('subscription.wait_listen', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // Not sure if its acceptable to have Request parsing here.
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $process->redirectUrl = $request->get('location', $request->getSchemeAndHttpHost());
        } else {
            $process->redirectUrl = $this->router->generate("homepage");
        }

        $process->clientId = $subscription->getUuid();
        $process->carrier  = $subscription->getSubscriptionPack()->getCarrierId();
        $process->userIp   = $subscription->getUser()->getIp();

        // The request headers of the end user.
        $currentUserRequestHeaders = '';
        if ($request && is_array($request->headers->all())) {
            foreach ($request->headers->all() as $key => $value) {
                $currentUserRequestHeaders .= "{$key}: {$value[0]}\r\n";
            }
        }
        $process->userHeaders = $currentUserRequestHeaders;
        $process->clientUser  = $subscription->getUser()->getIdentifier();

        return $process;
    }
}