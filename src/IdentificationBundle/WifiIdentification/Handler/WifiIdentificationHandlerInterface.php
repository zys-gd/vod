<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:55
 */

namespace IdentificationBundle\WifiIdentification\Handler;


use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

interface WifiIdentificationHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool;

    public function getRedirectUrl();

    public function areSMSSentByBilling(): bool;

    public function getExistingUser(string $msisdn): ?User;

    public function getPhoneValidationOptions(): PhoneValidationOptions;
}