<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:53
 */

namespace IdentificationBundle\Carriers\MobilinkPK;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\CommonFlow\HasCustomPixelIdent;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\HasPostPaidRestriction;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Request;

class MobilinkPKIdentificationHandler implements
    IdentificationHandlerInterface,
    HasCommonFlow,
    HasCustomPixelIdent,
    HasPostPaidRestriction
{

    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }


    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::MOBILINK_PAKISTAN;
    }

    public function getAdditionalIdentificationParams(Request $request): array
    {
        return [];
    }

    public function onConfirm(ProcessResult $processResult): void
    {
        // TODO: Implement onConfirm() method.
    }

    public function getExistingUser(string $msisdn): ?User
    {
        return $this->repository->findOneByMsisdn($msisdn);
    }
}