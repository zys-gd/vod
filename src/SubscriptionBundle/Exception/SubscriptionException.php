<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 11/08/17
 * Time: 1:17 PM
 */

namespace SubscriptionBundle\Exception;



use Symfony\Component\HttpFoundation\JsonResponse;

class SubscriptionException extends \Exception
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