<?php

namespace IdentificationBundle\Listener;

use App\Controller\HomeController;
use IdentificationBundle\Identification\Handler\AlreadySubscribedHandlerProvider;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AlreadySubscribedListener
 */
class AlreadySubscribedListener
{
    /**
     * @var IdentificationStatus
     */
    private $identificationStatus;

    /**
     * @var AlreadySubscribedHandlerProvider
     */
    private $handlerProvider;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * AlreadySubscribedListener constructor
     *
     * @param IdentificationStatus $identificationStatus
     * @param AlreadySubscribedHandlerProvider $handlerProvider
     * @param SessionInterface $session
     * @param RouterInterface $router
     */
    public function __construct(
        IdentificationStatus $identificationStatus,
        AlreadySubscribedHandlerProvider $handlerProvider,
        SessionInterface $session,
        RouterInterface $router
    ) {
        $this->identificationStatus = $identificationStatus;
        $this->handlerProvider = $handlerProvider;
        $this->session = $session;
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $args = $event->getController();

        if (is_array($args)) {
            $controller = $args[0] ?? null;
            $method     = $args[1] ?? null;
        } else {
            $method = $controller = $args;
        }

        if (!$controller || !$method) {
            return;
        }

        $request = $event->getRequest();
        $params = $request->query->all();
        $errorHandler = empty($params['err_handle']) ? null : $params['err_handle'];

        if (!($controller instanceof HomeController)
            || $this->identificationStatus->isIdentified()
            || ($errorHandler && $errorHandler !== 'already_subscribed')
        ) {
            return;
        }

        if (!$ispData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session)) {
            return;
        }

        if (!$handler = $this->handlerProvider->get($ispData['carrier_id'])) {
            return;
        }

        try {
            $handler->process($request);
        } catch (\Exception $exception) {

        }
    }
}