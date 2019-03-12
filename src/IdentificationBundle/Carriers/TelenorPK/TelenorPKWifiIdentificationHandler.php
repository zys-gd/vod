<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.03.19
 * Time: 12:29
 */

namespace IdentificationBundle\Carriers\TelenorPK;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

class TelenorPKWifiIdentificationHandler implements WifiIdentificationHandlerInterface
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
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::TELENOR_PAKISTAN_DOT;
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
        $modifiedMsisdn = mb_strcut($msisdn, 0, 15);

        return $this->repository->findOneByPartialMsisdnMatch($modifiedMsisdn);
    }
}