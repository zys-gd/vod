<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 11:38
 */

namespace IdentificationBundle\Controller;


use SubscriptionBundle\Controller\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CallbackController
{
    use ResponseTrait;

    /**
     * @Route("/api/v0/listen/identify")
     * @param Request $request
     * @return JsonResponse
     */
    public function listenIdentifyAction(Request $request)
    {
        return $this->getSimpleJsonResponse('Identified');
    }

}