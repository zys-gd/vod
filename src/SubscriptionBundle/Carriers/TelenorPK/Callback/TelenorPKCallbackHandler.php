<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.03.19
 * Time: 17:04
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Callback;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Service\Callback\Impl\HasCommonFlow;
use Symfony\Component\HttpFoundation\Request;

class TelenorPKCallbackHandler implements CarrierCallbackHandlerInterface, HasCommonFlow
{
    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * TelenorPKCallbackHandler constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        // TODO: Implement afterProcess() method.
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ConstBillingCarrierId::TELENOR_PAKISTAN_DOT;
    }

    public function getUser(string $msisdn): ?User
    {
        $modifiedMsisdn = mb_strcut($msisdn, 0, 15);

        return $this->userRepository->findOneByPartialMsisdnMatch($modifiedMsisdn);
    }
}