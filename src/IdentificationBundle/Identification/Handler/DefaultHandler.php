<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:40
 */

namespace IdentificationBundle\Identification\Handler;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class DefaultHandler implements IdentificationHandlerInterface, HasCommonFlow
{
    /**
     * @var UserRepository
     */
    private $repository;


    /**
     * DefaultHandler constructor.
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
    }

    public function getAdditionalIdentificationParams(Request $request): array
    {
        return [];
    }

    public function getExistingUser(string $msisdn): ?User
    {
        return $this->repository->findOneByMsisdn($msisdn);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function needHandle(Request $request): bool
    {
        return true;
    }
}