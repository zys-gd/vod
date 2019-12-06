<?php

namespace SubscriptionBundle\Carriers\OrangeTN\Callback;

use AppBundle\Constant\Carrier;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomConversionTrackingRules;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 11:18
 */
class OrangeTNSubscribeCallbackHandler implements CarrierCallbackHandlerInterface, HasCustomConversionTrackingRules, HasCommonFlow
{
    private $userRepository;


    /**
     * EtisalatEGCallbackSubscribe constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId == ID::ORANGE_TUNISIA;
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        // TODO: Implement onRenewSendSuccess() method.
    }

    public function isConversionNeedToBeTracked(ProcessResult $result): bool
    {
        return true;
    }

    public function getUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }
}