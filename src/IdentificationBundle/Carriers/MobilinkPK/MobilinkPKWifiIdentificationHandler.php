<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.02.19
 * Time: 12:57
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
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
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::MOBILINK_PAKISTAN;
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
            '+9230XXXXXXXX',
            '^\+9230[0-9]{8}$',
            'XXXXX',
            '^[0-9]{1,5}$'
        );
    }
}