<?php


namespace IdentificationBundle\Carriers\OrangeTNMM;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

class OrangeTNMMWifiIdentificationHandler implements WifiIdentificationHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        // TODO: Implement canHandle() method.
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
}