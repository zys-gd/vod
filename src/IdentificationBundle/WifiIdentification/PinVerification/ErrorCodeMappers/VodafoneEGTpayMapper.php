<?php


namespace IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeMappers;


use App\Domain\Constants\ConstBillingCarrierId;

/**
 * This is example
 *
 * Class VodafoneEGTpayMapper
 * @package IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeMappers
 */
class VodafoneEGTpayMapper implements ErrorCodeMapperInterface
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
        return $billingCarrierId === ConstBillingCarrierId::VODAFONE_EGYPT_TPAY;
    }
}