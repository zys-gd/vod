<?php

namespace SubscriptionBundle\Carriers\TMobilePolandDimoco\Callback;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCommonFlow;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TMobilePolandDimocoCallbackHandler
 */
class TMobilePolandDimocoCallbackHandler implements CarrierCallbackHandlerInterface, HasCommonFlow
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * TMobilePolandDimocoCallbackHandler constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @param int     $carrierId
     *
     * @return bool
     */
    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ID::TMOBILE_POLAND_DIMOCO;
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function getUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        // TODO: Implement afterProcess() method.
    }
}