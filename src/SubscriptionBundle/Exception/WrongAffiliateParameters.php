<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 20-03-19
 * Time: 10:00
 */

namespace SubscriptionBundle\Exception;


use Symfony\Component\HttpFoundation\JsonResponse;

class WrongAffiliateParameters extends \ErrorException
{
    /** @var  JsonResponse */
    protected  $response;

    /**
     * @return JsonResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param JsonResponse $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

}