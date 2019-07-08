<?php

namespace IdentificationBundle\WifiIdentification\Common;

use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Service\ZeroCreditSubscriptionChecking;

/**
 * Class RequestProvider
 */
class RequestProvider
{
    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;

    /**
     * RequestProvider constructor
     *
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     */
    public function __construct(ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking)
    {
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
    }

    /**
     * @param string $msisdn
     * @param CarrierInterface $carrier
     * @param string $body
     * @param array $additionalParameters
     *
     * @return ProcessRequestParameters
     */
    public function getPinRequestParameters(
        string $msisdn,
        CarrierInterface $carrier,
        string $body,
        array $additionalParameters
    ): ProcessRequestParameters
    {
        $parameters = new ProcessRequestParameters();

        $parameters->client = 'vod-store';
        $parameters->additionalData = array_merge(
            [
                'body' => $body,
                'msisdn' => $msisdn,
                'carrier' => $carrier->getBillingCarrierId(),
                'op_id' => $carrier->getOperatorId(),
                'zero_credit_sub_available' => $this->zeroCreditSubscriptionChecking->isAvailable($carrier)
            ],
            $additionalParameters
        );

        return $parameters;
    }

    /**
     * @param string $msisdn
     * @param CarrierInterface $carrier
     * @param string $pinCode
     * @param string $clientUser
     * @param array $additionalParameters
     *
     * @return ProcessRequestParameters
     */
    public function getPinVerifyParameters(
        string $msisdn,
        CarrierInterface $carrier,
        string $pinCode,
        string $clientUser,
        array $additionalParameters
    ): ProcessRequestParameters
    {
        $parameters = new ProcessRequestParameters();

        $parameters->client = 'vod-store';
        $parameters->additionalData = array_merge(
            [
                'msisdn' => $msisdn,
                'carrier' => $carrier->getBillingCarrierId(),
                'op_id' => $carrier->getOperatorId(),
                'pin_code' => $pinCode,
                'client_user' => $clientUser,
                'zero_credit_sub_available' => $this->zeroCreditSubscriptionChecking->isAvailable($carrier)
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