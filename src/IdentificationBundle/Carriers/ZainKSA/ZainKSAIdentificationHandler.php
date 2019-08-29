<?php

namespace IdentificationBundle\Carriers\ZainKSA;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\CommonFlow\HasCustomPixelIdent;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Request;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;

/**
 * Class ZainSAIdentificationHandler
 */
class ZainKSAIdentificationHandler implements IdentificationHandlerInterface, HasCommonFlow, HasCustomPixelIdent
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ZainSAIdentificationHandler constructor.
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
        return $carrier->getBillingCarrierId() === ID::ZAIN_SAUDI_ARABIA;
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

    /**
     * @param ProcessResult $processResult
     */
    public function onConfirm(ProcessResult $processResult): void
    {
        // TODO: Implement onConfirm() method.
    }
}