<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.10.18
 * Time: 9:52
 */

namespace SubscriptionBundle\Service\Callback\Impl;


use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;

class DefaultHandler implements CarrierCallbackHandlerInterface, HasCommonFlow
{
    private $userRepository;


    /**
     * DefaultHandler constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return true;
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        // TODO: Implement onRenewSendSuccess() method.
    }

    public function getUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }
}