<?php

namespace IdentificationBundle\Carriers\VodafoneEGTpay;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeMappers\ErrorCodeMapperInterface;

/**
 * Class VodafoneEGTpayErrorCodeMapper
 */
class VodafoneEGTpayErrorCodeMapper implements ErrorCodeMapperInterface
{
    /**
     * @param int $billingResponseCode
     *
     * @return int
     */
    public function map(int $billingResponseCode): int
    {
        switch ($billingResponseCode) {
            default:
                return $billingResponseCode;
        }
    }

    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::VODAFONE_EGYPT_TPAY;
    }
}