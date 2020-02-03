<?php

namespace Carriers\VodafoneDEDimoco\Identification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

/**
 * Class VodafoneDEDimocoIdentificationHandler
 */
class VodafoneDEDimocoIdentificationHandler implements WifiIdentificationHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * DebitelDEDimocoWifiIdentificationHandler constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::VODAFONE_DE_DIMOCO;
    }

    /**
     * @return bool
     */
    public function areSMSSentByBilling(): bool
    {
        return false;
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function getExistingUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }

    /**
     * @return PhoneValidationOptions
     */
    public function getPhoneValidationOptions(): PhoneValidationOptions
    {
        return new PhoneValidationOptions(
            '+49XXXXXXXXXXX',
            '^\+49[0-9]{11}$',
            'XXXX',
            '^[0-9]{4}$'
        );
    }

    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }
}