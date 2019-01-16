<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 12:09
 */

namespace IdentificationBundle\Identification\Controller;


use IdentificationBundle\Identification\Service\RouteProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CommonFlowController extends AbstractController
{
    /**
     * @var RouteProvider
     */
    private $routeProvider;

    /**
     * CommonFlowController constructor.
     */
    public function __construct(RouteProvider $routeProvider)
    {
        $this->routeProvider = $routeProvider;
    }


    /**
     * @Route("/wait-for-callback",name="wait_for_callback")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function waitForCallbackAction(Request $request)
    {

        if (!$successUrl = $request->get('successUrl', '')) {
            throw new BadRequestHttpException('`successUrl` is required');
        }

        return $this->render('@Identification/commonFlow/wait_for_callback.twig', [
            'successUrl' => $successUrl,
            'failureUrl' => $this->routeProvider->getLinkToHomepage(['err' => 'wait_for_callback_timeout'])
        ]);
    }

    /**
     * @Route("/get-status",name="callback_status")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStatusAction(Request $request)
    {
        return new JsonResponse(['result' => (bool)mt_rand(0, 0)]);
    }
}