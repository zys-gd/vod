<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.02.19
 * Time: 12:57
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

class MobilinkPKWifiIdentificationHandler implements WifiIdentificationHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::MOBILINK_PAKISTAN;
    }

    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }

    public function areSMSSentByBilling(): bool
    {
        return false;
    }
}