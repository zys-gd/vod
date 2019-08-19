<?php

namespace IdentificationBundle\WifiIdentification\Common;

use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;

/**
 * Class RequestProvider
 */
class RequestProvider
{
    /**
     * @param string $msisdn
     * @param int    $carrierId
     * @param string $operatorId
     * @param string $body
     * @param array  $additionalParameters
     * @param bool   $isZeroCreditSubAvailable
     *
     * @return ProcessRequestParameters
     */
    public function getPinRequestParameters(
        string $msisdn,
        int $carrierId,
        string $operatorId,
        string $body,
        array $additionalParameters,
        bool $isZeroCreditSubAvailable
    ): ProcessRequestParameters
    {
        $parameters = new ProcessRequestParameters();

        $parameters->client = 'vod-store';
        $parameters->zeroCreditSubAvailable = $isZeroCreditSubAvailable;

        $parameters->additionalData = array_merge(
            [
                'body' => $body,
                'msisdn' => $msisdn,
                'carrier' => $carrierId,
                'op_id' => $operatorId
            ],
            $additionalParameters
        );

        return $parameters;
    }

    /**
     * @param string $msisdn
     * @param int    $carrierId
     * @param string $operatorId
     * @param string $pinCode
     * @param string $clientUser
     * @param array  $additionalParameters
     * @param bool   $isZeroCreditSubAvailable
     *
     * @return ProcessRequestParameters
     */
    public function getPinVerifyParameters(
        string $msisdn,
        int $carrierId,
        string $operatorId,
        string $pinCode,
        string $clientUser,
        array $additionalParameters,
        bool $isZeroCreditSubAvailable
    ): ProcessRequestParameters
    {
        $parameters = new ProcessRequestParameters();

        $parameters->client = 'vod-store';
        $parameters->zeroCreditSubAvailable = $isZeroCreditSubAvailable;

        $parameters->additionalData = array_merge(
            [
                'msisdn' => $msisdn,
                'carrier' => $carrierId,
                'op_id' => $operatorId,
                'pin_code' => $pinCode,
                'client_user' => $clientUser
            ],
            $additionalParameters
        );

        return $parameters;
    }

    /**
     * @param int $carrierId
     * @param string $operatorId
     * @param string $pinCode
     * @param array $additionalParameters
     *
     * @return ProcessRequestParameters
     */
    public function getPinResendParameters(
        int $carrierId,
        string $operatorId,
        string $pinCode,
        array $additionalParameters
    ): ProcessRequestParameters
    {
        $parameters = new ProcessRequestParameters();
        $parameters->client = 'vod-store';

        $parameters->additionalData = array_merge(
            [
                'carrier'     => $carrierId,
                'op_id'       => $operatorId,
                'pin_code'    => $pinCode
            ],
            $additionalParameters
        );

        return $parameters;
    }
}