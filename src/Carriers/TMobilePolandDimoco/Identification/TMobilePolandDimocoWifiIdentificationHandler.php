<?php

namespace Carriers\TMobilePolandDimoco\Identification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

/**
 * Class TMobilePolandDimocoWifiIdentificationHandler
 */
class TMobilePolandDimocoWifiIdentificationHandler implements WifiIdentificationHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * TMobilePolandDimocoWifiIdentificationHandler constructor
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TMOBILE_POLAND_DIMOCO;
    }

    /**
     * @return PhoneValidationOptions
     */
    public function getPhoneValidationOptions(): PhoneValidationOptions
    {
        return new PhoneValidationOptions(
            '+48XXXXXXXXXX',
            '^\+48[0-9]{10,14}$',
            'XXXXX',
            '^[0-9]{1,5}$'
        );
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function getExistingUser(string $msisdn): ?User
    {
        return $this->repository->findOneByMsisdn($msisdn);
    }

    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }

    public function areSMSSentByBilling(): bool
    {
        // TODO: Implement areSMSSentByBilling() method.
    }
}