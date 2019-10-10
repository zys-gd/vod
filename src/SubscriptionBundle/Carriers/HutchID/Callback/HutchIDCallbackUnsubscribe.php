<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomFlow;
use SubscriptionBundle\Subscription\Unsubscribe\Unsubscriber;
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
     * @var Unsubscriber
     */
    private $unsubscriber;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;

    /**
     * HutchIDCallbackUnsubscribe constructor.
     *
     * @param UserRepository         $userRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param Unsubscriber           $unsubscriber
     * @param ApiConnector           $apiConnector
     */
    public function __construct(
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        Unsubscriber $unsubscriber,
        ApiConnector $apiConnector
    )
    {
        $this->userRepository         = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->unsubscriber           = $unsubscriber;
        $this->crossSubscriptionApi   = $apiConnector;
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
        /** @var User $user */
        $user = $this->userRepository->findOneByMsisdn($requestParams->provider_user);

        /** @var Subscription $subscription */
        $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

        /** @var ProcessResult $processResult */
        $processResult = $this->unsubscriber->unsubscribe($subscription, $subscription->getSubscriptionPack());

        if ($processResult->isSuccessful() && $processResult->isFinal()) {
            $this->unsubscriber->trackEventsForUnsubscribe($subscription, $processResult);
            $this->crossSubscriptionApi->deregisterSubscription($user->getIdentifier(), $user->getBillingCarrierId());
        }

        return $subscription;
    }
}