<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.05.19
 * Time: 17:32
 */

namespace App\Domain\ACL;


use App\Domain\ACL\Accessors\SubscribeExternalAPICheck;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Service\SubscriptionVoter\SubscriptionVoterInterface;
use Symfony\Component\HttpFoundation\Request;

class SubscribeACL implements SubscriptionVoterInterface
{
    /**
     * @var SubscribeExternalAPICheck
     */
    private $APICheck;
    /**
     * @var UserRepository
     */
    private $repository;


    /**
     * SubscribeACL constructor.
     * @param SubscribeExternalAPICheck $APICheck
     */
    public function __construct(SubscribeExternalAPICheck $APICheck, UserRepository $repository)
    {
        $this->APICheck   = $APICheck;
        $this->repository = $repository;
    }

    public function checkIfSubscriptionAllowed(Request $request, IdentificationData $identificationData, ISPData $ISPData): bool
    {
        $user = $this->repository->findOneByIdentificationToken($identificationData->getIdentificationToken());
        if (!$user) {
            return false;
        }

        $isExists = $this->APICheck->checkOnExternalAPI($user->getIdentifier(), 0);

        return !$isExists;
    }
}