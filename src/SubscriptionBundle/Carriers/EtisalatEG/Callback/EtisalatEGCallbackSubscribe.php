<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 11:16
 */

namespace SubscriptionBundle\Carriers\EtisalatEG\Callback;


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

class EtisalatEGCallbackSubscribe implements CarrierCallbackHandlerInterface, HasCustomConversionTrackingRules, HasCommonFlow
{
    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * EtisalatEGCallbackSubscribe constructor.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId == ID::ETISALAT_EGYPT;
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