<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 11:08
 */

namespace IdentificationBundle\WifiIdentification\Common;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;

class RequestProvider
{
    public function getPinRequestParameters(
        string $msisdn,
        int $carrierId,
        string $operatorId,
        string $body,
        array $additionalParameters
    ): ProcessRequestParameters
    {

        $parameters                 = new ProcessRequestParameters();
        $parameters->additionalData = array_merge(
            [
                'body'    => $body,
                'msisdn'  => $msisdn,
                'carrier' => $carrierId,
                'op_id'   => $operatorId,
            ],
            $additionalParameters
        );
        return $parameters;
    }

    public function getPinVerifyParameters(
        string $msisdn,
        int $carrierId,
        string $operatorId,
        string $pinCode,
        string $clientUser,
        array $additionalParameters
    ): ProcessRequestParameters
    {

        $parameters                 = new ProcessRequestParameters();
        $parameters->additionalData = array_merge(
            [
                'msisdn'      => $msisdn,
                'carrier'     => $carrierId,
                'op_id'       => $operatorId,
                'pin_code'    => $pinCode,
                'client_user' => $clientUser
            ],
            $additionalParameters

        );
        return $parameters;

    }
}