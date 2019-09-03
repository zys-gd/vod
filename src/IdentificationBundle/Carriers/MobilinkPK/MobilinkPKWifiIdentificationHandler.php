<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.02.19
 * Time: 12:57
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\HasPostPaidRestriction;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

class MobilinkPKWifiIdentificationHandler implements WifiIdentificationHandlerInterface, HasPostPaidRestriction
{
    /**
     * @var UserRepository
     */
    private $repository;


    /**
     * MobilinkPKWifiIdentificationHandler constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::MOBILINK_PAKISTAN;
    }

    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }

    public function areSMSSentByBilling(): bool
    {
        return false;
    }

    public function getExistingUser(string $msisdn): ?User
    {
        return $this->repository->findOneByMsisdn($msisdn);
    }

    public function getPhoneValidationOptions(): PhoneValidationOptions
    {
        return new PhoneValidationOptions(
            '+923XXXXXXXXX',
            '^\+923[0-9]{9}$',
            'XXXXX',
            '^[0-9]{1,5}$'
        );
    }
}