<?php

namespace Carriers\TMobilePolandDimoco\Callback;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitCompleter;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomTrackingRules;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TMobilePolandDimocoCallbackHandler
 */
class TMobilePolandDimocoCallbackHandler implements CarrierCallbackHandlerInterface, HasCommonFlow, HasCustomTrackingRules
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SubscriptionLimitCompleter
     */
    private $subscriptionLimitCompleter;

    /**
     * @var ApiConnector
     */
    private $apiConnector;

    /**
     * TMobilePolandDimocoCallbackHandler constructor.
     *
     * @param UserRepository             $userRepository
     * @param SubscriptionLimitCompleter $subscriptionLimitCompleter
     * @param ApiConnector               $apiConnector
     */
    public function __construct(
        UserRepository $userRepository,
        SubscriptionLimitCompleter $subscriptionLimitCompleter,
        ApiConnector $apiConnector
    ) {
        $this->userRepository = $userRepository;
        $this->subscriptionLimitCompleter = $subscriptionLimitCompleter;
        $this->apiConnector = $apiConnector;
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

    /**
     * @param Subscription  $subscription
     * @param User          $User
     * @param ProcessResult $processResponse
     */
    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        $this->subscriptionLimitCompleter->finishProcess($processResponse, $subscription);

        $user = $subscription->getUser();

        $this->apiConnector->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
    }

    /**
     * @param ProcessResult $result
     *
     * @return bool
     */
    public function isNeedToBeTracked(ProcessResult $result): bool
    {
        return true;
    }
}