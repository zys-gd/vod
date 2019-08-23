<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\Callback\Common\CommonFlowHandler;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Service\Callback\Impl\HasCustomFlow;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\SubscriptionCreator;
use Symfony\Component\HttpFoundation\Request;

class HutchIDCallbackSubscribe implements CarrierCallbackHandlerInterface, HasCustomFlow
{
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SubscriptionCreator
     */
    private $subscriptionCreator;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var SubscriptionPackRepository
     */
    private $subscriptionPackRepository;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    /**
     * HutchIDCallbackSubscribe constructor.
     *
     * @param CommonFlowHandler          $commonFlowHandler
     * @param UserRepository             $userRepository
     * @param SubscriptionCreator        $subscriptionCreator
     * @param SubscriptionRepository     $subscriptionRepository
     * @param SubscriptionPackRepository $subscriptionPackRepository
     * @param EntitySaveHelper           $entitySaveHelper
     */
    public function __construct(
        CommonFlowHandler $commonFlowHandler,
        UserRepository $userRepository,
        SubscriptionCreator $subscriptionCreator,
        SubscriptionRepository $subscriptionRepository,
        SubscriptionPackRepository $subscriptionPackRepository,
        EntitySaveHelper $entitySaveHelper
    )
    {
        $this->commonFlowHandler          = $commonFlowHandler;
        $this->userRepository             = $userRepository;
        $this->subscriptionCreator        = $subscriptionCreator;
        $this->subscriptionRepository     = $subscriptionRepository;
        $this->subscriptionPackRepository = $subscriptionPackRepository;
        $this->entitySaveHelper           = $entitySaveHelper;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ConstBillingCarrierId::HUTCH_INDONESIA;
    }

    /**
     * @param Request $request
     * @param string  $type
     *
     * @throws \SubscriptionBundle\Exception\SubscriptionException
     */
    public function process(Request $request, string $type)
    {
        $requestParams = (Object)$request->request->all();
        $user          = $this->userRepository->findOneByMsisdn($requestParams->provider_user);
        /** @var Subscription $subscription */
        $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

        if (!$subscription) {
            $carrier = $user->getCarrier();
            /** @var SubscriptionPack $subscriptionPack */
            $subscriptionPack = $this->subscriptionPackRepository->findOneBy(['carrier' => $carrier, 'status' => 1]);

            $subscription = $this->subscriptionCreator->create($user, $subscriptionPack);
            $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
            $this->entitySaveHelper->persistAndSave($subscription);
        }

        $this->commonFlowHandler->process($request, ConstBillingCarrierId::HUTCH_INDONESIA, $type);
    }
}