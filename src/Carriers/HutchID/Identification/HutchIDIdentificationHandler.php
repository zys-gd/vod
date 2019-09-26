<?php

namespace Carriers\HutchID\Identification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\CommonFlow\HasCustomPixelIdent;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Request;


class HutchIDIdentificationHandler implements
    IdentificationHandlerInterface,
    HasCustomPixelIdent,
    HasCommonFlow
{
    /**
     * @var UserRepository
     */
    private $repository;

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
        return $carrier->getBillingCarrierId() === ID::HUTCH3_INDONESIA_DOT;
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

    public function onConfirm(ProcessResult $processResult): void
    {
        // TODO: Implement onConfirm() method.
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