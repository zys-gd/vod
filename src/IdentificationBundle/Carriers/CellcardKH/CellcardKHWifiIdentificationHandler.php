<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.10.19
 * Time: 17:17
 */

namespace IdentificationBundle\Carriers\CellcardKH;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use IdentificationBundle\WifiIdentification\Handler\HasCustomMsisdnCleaning;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

class CellcardKHWifiIdentificationHandler implements WifiIdentificationHandlerInterface, HasCustomMsisdnCleaning
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() == ID::CELLCARD_CAMBODIA;
    }

    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }

    public function areSMSSentByBilling(): bool
    {
        // TODO: Implement areSMSSentByBilling() method.
    }

    public function getExistingUser(string $msisdn): ?User
    {
        // TODO: Implement getExistingUser() method.
    }

    public function getPhoneValidationOptions(): PhoneValidationOptions
    {
        // TODO: Implement getPhoneValidationOptions() method.
    }

    public function cleanMsisdn(string $msisdn): string
    {
        return str_replace('+', '', $msisdn);
    }
}