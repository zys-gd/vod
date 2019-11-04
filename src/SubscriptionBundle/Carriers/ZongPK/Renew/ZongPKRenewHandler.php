<?php


namespace SubscriptionBundle\Carriers\ZongPK\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeFacade;
use Symfony\Component\HttpFoundation\Request;

class ZongPKCallbackRenew implements CarrierCallbackHandlerInterface, HasCommonFlow
{
    /**
     * @var Notifier
     */
    private $notifier;
    /**
     * @var UnsubscribeFacade
     */
    private $unsubscribeFacade;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ZongPKCallbackRenew constructor.
     *
     * @param UnsubscribeFacade $unsubscribeFacade
     * @param Notifier          $notifier
     * @param UserRepository    $userRepository
     */
    public function __construct(
        UnsubscribeFacade $unsubscribeFacade,
        Notifier $notifier,
        UserRepository $userRepository
    )
    {
        $this->notifier          = $notifier;
        $this->unsubscribeFacade = $unsubscribeFacade;
        $this->userRepository    = $userRepository;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ID::ZONG_PAKISTAN;
    }

    /**
     * @param Subscription  $subscription
     * @param User          $User
     * @param ProcessResult $processResponse
     *
     * @throws \SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException
     */
    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        if ($subscription->isSubscribed()) {
            $this->notifier->sendNotification(
                'notify_renew',
                $subscription,
                $subscription->getSubscriptionPack(),
                $subscription->getSubscriptionPack()->getCarrier()
            );
        }

        if ($subscription->isNotEnoughCredit()) {
            $this->notifier->sendNotification(
                'renewal_failure',
                $subscription,
                $subscription->getSubscriptionPack(),
                $subscription->getSubscriptionPack()->getCarrier()
            );
            $this->unsubscribeFacade->doFullUnsubscribe($subscription);
        }
    }

    public function getUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }
}