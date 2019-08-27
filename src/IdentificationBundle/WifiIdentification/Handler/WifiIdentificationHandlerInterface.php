<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:55
 */

namespace IdentificationBundle\WifiIdentification\Handler;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;

interface WifiIdentificationHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool;

    public function getRedirectUrl();

    public function areSMSSentByBilling(): bool;

    public function getExistingUser(string $msisdn): ?User;

    public function getPhoneValidationOptions(): PhoneValidationOptions;

}