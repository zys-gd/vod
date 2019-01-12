<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 17:17
 */

namespace IdentificationBundle\Carriers\EtisalatEG;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

class EtisalatEGWifiIdentificationHandler implements
    WifiIdentificationHandlerInterface

{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return ConstBillingCarrierId::ETISALAT_EGYPT === $carrier->getBillingCarrierId();
    }


    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }

    public function isPinSendAllowed($mobileNumber): bool
    {
        return true;
    }

    public function areSMSSentByBilling(): bool
    {
        return false;
    }
}