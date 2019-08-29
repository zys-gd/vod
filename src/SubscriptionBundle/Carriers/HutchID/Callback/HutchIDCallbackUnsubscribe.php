<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\User\Service\UserFactory;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Carriers\HutchID\Subscribe\HutchIDSMSSubscriber;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Piwik\DataMapper\ConversionEventMapper;
use SubscriptionBundle\Piwik\EventPublisher;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Callback\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Callback\Common\Type\SubscriptionCallbackHandler;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomFlow;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomTrackingRules;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
use SubscriptionBundle\Subscription\Notification\Notifier;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Subscription\Unsubscribe\Unsubscriber;

class HutchIDCallbackUnsubscribe implements CarrierCallbackHandlerInterface, HasCustomFlow
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var Unsubscriber
     */
    private $unsubscriber;

    /**
     * HutchIDCallbackUnsubscribe constructor.
     *
     * @param UserRepository         $userRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param Unsubscriber           $unsubscriber
     */
    public function __construct(
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        Unsubscriber $unsubscriber
    )
    {
        $this->userRepository         = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->unsubscriber           = $unsubscriber;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ID::HUTCH_INDONESIA;
    }

    /**
     * @param Request $request
     * @param string  $type
     *
     * @return Subscription
     * @throws \Exception
     */
    public function process(Request $request, string $type)
    {
        $requestParams = (Object)$request->request->all();
        /** @var User $user */
        $user = $this->userRepository->findOneByMsisdn($requestParams->provider_user);

        /** @var Subscription $subscription */
        $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

        $this->unsubscriber->unsubscribe($subscription, $subscription->getSubscriptionPack());

        return $subscription;
    }
}