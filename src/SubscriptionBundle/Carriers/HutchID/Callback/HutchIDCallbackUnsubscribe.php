<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomFlow;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeFacade;
use Symfony\Component\HttpFoundation\Request;

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
     * @var UnsubscribeFacade
     */
    private $unsubscribeFacade;

    /**
     * HutchIDCallbackUnsubscribe constructor.
     *
     * @param UserRepository         $userRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param UnsubscribeFacade      $unsubscribeFacade
     */
    public function __construct(
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        UnsubscribeFacade $unsubscribeFacade
    )
    {
        $this->userRepository         = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->unsubscribeFacade      = $unsubscribeFacade;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ID::HUTCH3_INDONESIA_DOT;
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

        $user         = $this->userRepository->findOneByMsisdn($requestParams->provider_user);
        $subscription = $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);

        $this->unsubscribeFacade->doFullUnsubscribe($subscription);

        return $subscription;
    }
}