<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 11:38
 */

namespace IdentificationBundle\Callback\Controller;


use IdentificationBundle\Callback\IdentCallbackProcessor;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class CallbackController
{


    use ResponseTrait;
    /**
     * @var IdentCallbackProcessor
     */
    private $callbackProcessor;

    /**
     * CallbackController constructor.
     * @param IdentCallbackProcessor $callbackProcessor
     */
    public function __construct(IdentCallbackProcessor $callbackProcessor)
    {
        $this->callbackProcessor = $callbackProcessor;
    }

    /**
     * @Route("/api/v2/listen/identify",name="identify_callback")
     * @param Request $request
     * @return JsonResponse
     */
    public function listenIdentifyAction(Request $request)
    {
        try {
            try {
                $encoder = new JsonEncoder();
                $request->request->replace($encoder->decode($request->getContent(), 'array'));
            } catch (\Exception $e) {
                throw new BadRequestHttpException('Cannot parse json content');
            }

            if (!$type = $request->get('type', null)) {
                throw new BadRequestHttpException('`type` parameter is required');
            }

            if (!$carrierId = $request->get('carrier')) {
                throw new BadRequestHttpException('`carrier` parameter should be present in callback response');
            }

            $this->callbackProcessor->process($type, (int)$carrierId, $request->request->all());
        } catch (\Throwable $throwable) {
            return $this->wrapException($throwable);
        }

        return $this->getSimpleJsonResponse('Request is updated');
    }
}