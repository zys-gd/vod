<?php

namespace IdentificationBundle\Carriers\TMobilePolandDimoco;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeMappers\ErrorCodeMapperInterface;
use IdentificationBundle\WifiIdentification\PinVerification\ErrorCodes;

/**
 * Class TMobilePolandDimocoErrorCodeMapper
 */
class TMobilePolandDimocoErrorCodeMapper implements ErrorCodeMapperInterface
{
    /**
     * @param int $billingResponseCode
     *
     * @return int
     */
    public function map(int $billingResponseCode): int
    {
        switch ($billingResponseCode) {
            case ErrorCodes::WRONG_PHONE_NUMBER:
                return ErrorCodes::INVALID_PIN;
            default:
                return $billingResponseCode;
        }
    }

    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::TMOBILE_POLAND_DIMOCO;
    }
}