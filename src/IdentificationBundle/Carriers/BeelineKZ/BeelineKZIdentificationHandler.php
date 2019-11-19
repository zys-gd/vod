<?php

namespace IdentificationBundle\Carriers\BeelineKZ;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BeelineKZIdentificationHandler
 */
class BeelineKZIdentificationHandler implements IdentificationHandlerInterface, HasCommonFlow
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * BeelineKZIdentificationHandler constructor
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
        return $carrier->getBillingCarrierId() === ID::BEELINE_KAZAKHSTAN_DOT;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request): array
    {
        return [];
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
}