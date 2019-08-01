<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.11.18
 * Time: 13:29
 */

namespace SubscriptionBundle\Tests\BillingFramework;

use GuzzleHttp\Psr7\Response;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class TestBillingResponseProvider
{
    public static function createSuccessfulRedirectResponse($type, $url): Response
    {
        return new Response(200, [], json_encode([
            'data' => [
                'type'    => $type,
                'subtype' => ProcessResult::PROCESS_SUBTYPE_REDIRECT,
                'status'  => ProcessResult::STATUS_SUCCESSFUL,
                'url'     => $url,
            ]
        ]));


    }

    public static function createSuccessfulFinalResponse($type): Response
    {
        return new Response(200, [], json_encode([
            'data' => [
                'type'    => $type,
                'subtype' => ProcessResult::PROCESS_SUBTYPE_FINAL,
                'status'  => ProcessResult::STATUS_SUCCESSFUL,
            ]
        ]));


    }
}