<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.03.19
 * Time: 12:29
 */

namespace IdentificationBundle\Carriers\TelenorPK;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use IdentificationBundle\WifiIdentification\Exception\WifiIdentConfirmException;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinVerifyRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

class TelenorPKWifiIdentificationHandler implements WifiIdentificationHandlerInterface, HasCustomPinVerifyRules
{
    /**
     * @var UserRepository
     */
    private $repository;


    /**
     * TelenorPKWifiIdentificationHandler constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TELENOR_PAKISTAN_DOT;
    }

    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }

    public function areSMSSentByBilling(): bool
    {
        return true;
    }

    public function getExistingUser(string $msisdn): ?User
    {
        $modifiedMsisdn = mb_strcut($msisdn, 0, 15);

        return $this->repository->findOneByPartialMsisdnMatch($modifiedMsisdn);
    }

    public function getAdditionalPinVerifyParams(PinRequestResult $pinRequestResult): array
    {
        return ['otpId' => $pinRequestResult->getRawData()['otpId']];
    }

    public function afterSuccessfulPinVerify(PinVerifyResult $parameters): void
    {
        // TODO: Implement afterSuccessfulPinVerify() method.
    }

    public function afterFailedPinVerify(\Exception $exception): void
    {
        // TODO: Implement afterFailedPinVerify() method.
    }

    public function getMsisdnFromResult(PinVerifyResult $pinVerifyResult, string $phoneNumber): string
    {
        if (!isset($pinVerifyResult->getRawData()['user_identifier'])) {
            throw new WifiIdentConfirmException('Missing user identifier in Telenor PK wifi pinVerify');
        }

        return $pinVerifyResult->getRawData()['user_identifier'];
    }

    public function getPhoneValidationOptions(): PhoneValidationOptions
    {
        return new PhoneValidationOptions(
            '',
            ''
        );
    }
}