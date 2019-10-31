<?php

namespace IdentificationBundle\Carriers\TMobilePolandDimoco;

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
            '^\+48[0-9]{9}$',
            'XXXX',
            '^[0-9]{4}$'
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

    /**
     * @return bool
     */
    public function areSMSSentByBilling(): bool
    {
        return false;
    }

    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }
}