<?php

namespace IdentificationBundle\Carriers\OrangeEG;

use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;

/**
 * Class OrangeEGWifiIdentificationHandler
 */
class OrangeEGWifiIdentificationHandler implements WifiIdentificationHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * VodafonePKWifiIdentificationHandler constructor
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
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::ORANGE_EGYPT_TPAY;
    }

    public function getRedirectUrl()
    {
    }

    /**
     * @return bool
     */
    public function areSMSSentByBilling(): bool
    {
        return true;
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
}