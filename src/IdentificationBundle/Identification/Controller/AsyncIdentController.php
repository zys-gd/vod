<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 12:09
 */

namespace IdentificationBundle\Identification\Controller;


use IdentificationBundle\Identification\Common\Async\AsyncIdentFinisher;
use IdentificationBundle\Identification\Common\Async\AsyncIdentStatusProvider;
use IdentificationBundle\Identification\Service\RouteProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AsyncIdentController extends AbstractController
{
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var AsyncIdentFinisher
     */
    private $identFinisher;
    /**
     * @var AsyncIdentStatusProvider
     */
    private $statusProvider;

    /**
     * AsyncIdentController constructor.
     *
     * @param RouteProvider             $routeProvider
     * @param AsyncIdentFinisher        $finisher
     * @param AsyncIdentStatusProvider  $statusProvider
     */
    public function __construct(
        RouteProvider $routeProvider,
        AsyncIdentFinisher $finisher,
        AsyncIdentStatusProvider $statusProvider
    ) {
        $this->routeProvider  = $routeProvider;
        $this->identFinisher  = $finisher;
        $this->statusProvider = $statusProvider;
    }

    /**
     * @Route("/async-ident/show-page",name="wait_for_callback")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function waitForCallbackAction(Request $request)
    {
        if (!$successUrl = $request->get('successUrl', '')) {
            throw new BadRequestHttpException('`successUrl` is required');
        }

        return $this->render('@Identification/asyncIdent/wait_for_callback.twig', [
            'successUrl' => $successUrl,
            'confirmUrl' => $this->generateUrl('confirm_async_ident'),
            'failureUrl' => $this->routeProvider->getLinkToHomepage([
                'err' => 'wait_for_callback_timeout'
            ])
        ]);
    }

    /**
     * @Method("GET")
     * @Route("/async-ident/status",name="callback_status")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStatusAction(Request $request)
    {
        try {
            $result = $this->statusProvider->isCallbackReceived();
            return new JsonResponse(['result' => $result]);
        } catch (\Exception $exception) {
            return new JsonResponse(['result' => false]);
        }

    }

    /**
     * @Method("POST")
     * @Route("/async-ident/confirm",name="confirm_async_ident")
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmIdentAction(Request $request)
    {
        try {
            $this->identFinisher->finish();
            return new JsonResponse(['result' => true]);
        } catch (\Exception $exception) {
            return new JsonResponse(['result' => false]);
        }
    }
}