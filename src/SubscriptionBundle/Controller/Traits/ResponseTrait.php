<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 03/08/17
 * Time: 1:32 PM
 */

namespace SubscriptionBundle\Controller\Traits;


use Symfony\Component\HttpFoundation\JsonResponse;

trait ResponseTrait
{
    /**
     * Form Json Response
     *
     * @param string $message
     * @param int $statusCode
     * @param array $response
     * @param array $data
     * @return JsonResponse
     * @internal param Request $request
     */
    public function getSimpleJsonResponse(
        string $message,
        $statusCode = 200,
        $response = [],
        $data = []) : JsonResponse
    {
        $data['status'] = 'failure';
        if ($message) {
            $data['message'] = $message;
        }
        if (200 === $statusCode) {
            $data['status'] = 'ok';
        }

        $response['data'] = $data;
        return new JsonResponse($response, $statusCode);
    }



    protected function wrapException(\Throwable $throwable)
    {
        return $this->getSimpleJsonResponse($throwable->getMessage(), 400);
    }
}