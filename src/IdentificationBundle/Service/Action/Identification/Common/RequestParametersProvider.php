<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 10:25
 */

namespace IdentificationBundle\Service\Action\Identification\Common;


use IdentificationBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class RequestParametersProvider
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * BillingFrameworkHelperService constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param RouterInterface          $router
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router
    )
    {

        $this->eventDispatcher = $eventDispatcher;
        $this->router          = $router;
    }

    /**
     * @param Request          $request
     * @param SessionInterface $session
     * @param array            $additionalData
     * @return ProcessRequestParameters
     */
    public function prepareRequestParameters(Request $request, SessionInterface $session, array $additionalData = []): ProcessRequestParameters
    {
        $identificationData = $session->get('identification_data');

        $parameters               = new ProcessRequestParameters();
        $parameters->listener     = ''/*$this->router->generate('talentica_subscription.listen', [], UrlGeneratorInterface::ABSOLUTE_URL)*/
        ;
        $parameters->client       = 'store';
        $parameters->listenerWait = ''/*$this->router->generate('talentica_subscription.wait_listen', [], UrlGeneratorInterface::ABSOLUTE_URL)*/
        ;

        // Not sure if its acceptable to have Request parsing here.
        if ($request) {
            $parameters->redirectUrl = $request->get('location', $request->getSchemeAndHttpHost());
        } else {
            $parameters->redirectUrl = ''/*$this->router->generate("homepage")*/
            ;
        }

        $parameters->clientId = $identificationData['identification_token'];
        $parameters->carrier  = $identificationData['carrier_id'];
        $parameters->userIp   = $request->getClientIp();

        // The request headers of the end user.
        $currentUserRequestHeaders = '';
        if ($request && is_array($request->headers->all())) {
            foreach ($request->headers->all() as $key => $value) {
                $currentUserRequestHeaders .= "{$key}: {$value[0]}\r\n";
            }
        }
        $parameters->userHeaders    = $currentUserRequestHeaders;
        $parameters->additionalData = $additionalData;

        return $parameters;
    }
}