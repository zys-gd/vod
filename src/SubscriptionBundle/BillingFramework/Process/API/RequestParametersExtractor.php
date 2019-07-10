<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 13:41
 */

namespace SubscriptionBundle\BillingFramework\Process\API;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;

class RequestParametersExtractor
{
    public function extractParameters(ProcessRequestParameters $processParameters): array
    {
        $defaults = [
            'client'                    => $processParameters->client,
            'listener'                  => $processParameters->listener,
            'user_headers'              => $processParameters->userHeaders,
            'client_user'               => $processParameters->clientUser,
            'user_ip'                   => $processParameters->userIp,
            'carrier'                   => $processParameters->carrier,
            'client_id'                 => $processParameters->clientId,
            'redirect_url'              => $processParameters->redirectUrl,
            'listener_wait'             => $processParameters->listenerWait,
            'charge_product'            => $processParameters->chargeProduct,
            'charge_tier'               => $processParameters->chargeTier,
            'charge_strategy'           => $processParameters->chargeStrategy,
            'zero_credit_sub_available' => $processParameters->zeroCreditSubAvailable
        ];

        return array_merge($defaults, $processParameters->additionalData);
    }

}