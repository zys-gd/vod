<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.01.19
 * Time: 17:13
 */

namespace ExtrasBundle\API\Utils;


use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseMaker
{
    public static function makeErrorResponse(string $errorCode, int $httpCode, string $message): JsonResponse
    {
        return new JsonResponse(['code' => $errorCode, 'message' => $message, 'result' => false], $httpCode);
    }

    public static function makeSuccessResponse()
    {
        return new JsonResponse(['result' => true, 'code' => 'success']);
    }
}