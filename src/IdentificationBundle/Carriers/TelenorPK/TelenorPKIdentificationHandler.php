<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.03.19
 * Time: 12:17
 */

namespace IdentificationBundle\Carriers\TelenorPK;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class TelenorPKIdentificationHandler implements IdentificationHandlerInterface, HasCommonFlow
{
    /**
     * @var UserRepository
     */
    private $repository;


    /**
     * TelenorPKIdentificationHandler constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TELENOR_PAKISTAN_DOT;
    }

    public function getAdditionalIdentificationParams(Request $request): array
    {
        return [];
    }

    public function getExistingUser(string $msisdn): ?User
    {
        $modifiedMsisdn = mb_strcut($msisdn, 0, 15);

        return $this->repository->findOneByPartialMsisdnMatch($modifiedMsisdn);
    }

}