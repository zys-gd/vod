<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 10:26
 */

namespace SubscriptionBundle\Carriers\OrangeEG\Callback;


use App\Domain\Constants\ConstBillingCarrierId;
use AppBundle\Constant\Carrier;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Service\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Service\Callback\Impl\HasCustomTrackingRules;
use Symfony\Component\HttpFoundation\Request;

class OrangeEGUnsubscribeCallbackHandler implements CarrierCallbackHandlerInterface, HasCommonFlow, HasCustomTrackingRules
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
        return $request->get('carrier') === ConstBillingCarrierId::ORANGE_EGYPT;
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        // TODO: Implement onRenewSendSuccess() method.
    }

    public function isNeedToBeTracked(ProcessResult $result): bool
    {
        return true;
    }

    public function getUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }
}