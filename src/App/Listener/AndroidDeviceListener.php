<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 16:57
 */

namespace App\Listener;


use App\Controller\AppControllerInterface;
use App\Domain\Service\DeviceDetection\MobileDetector;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\RouterInterface;

class AndroidDeviceListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * AndroidDeviceListener constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event)
    {

        if (!$event->isMasterRequest()) {
            return;
        }


        $request = $event->getRequest();
        $session = $request->getSession();

        if ($request->isXmlHttpRequest()) {
            return;
        }


        $isForce = $request->get('f', '');
        if ($isForce) {
            $session->set('force', true);
            return;
        }

        $isForceSetPreviously = $session->get('force', false);
        if ($isForceSetPreviously) {
            return;
        }



        $args = $event->getController();
        if (is_array($args)) {
            $controller = $args[0] ?? null;
        }

        if (isset($controller) && !($controller instanceof AppControllerInterface)) {
            return;
        }

        $wrosRoute = $this->router->generate('wrong_os');
        if ($request->getPathInfo() == $wrosRoute) {
            return;
        }

        $mobileDetector = new MobileDetector();
        if (!$mobileDetector->isAndroidOS()) {
            $event->setController(function () use ($wrosRoute) {
                return new RedirectResponse($wrosRoute);
            });

            $event->stopPropagation();
        }
    }
}