<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\User\Service\UserFactory;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Callback\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomFlow;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
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
     * @var SubscriptionFactory
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
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var TokenGenerator
     */
    private $generator;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;

    /**
     * HutchIDCallbackSubscribe constructor.
     *
     * @param CommonFlowHandler          $commonFlowHandler
     * @param UserRepository             $userRepository
     * @param SubscriptionFactory        $subscriptionCreator
     * @param SubscriptionRepository     $subscriptionRepository
     * @param SubscriptionPackRepository $subscriptionPackRepository
     * @param EntitySaveHelper           $entitySaveHelper
     * @param CarrierRepositoryInterface $carrierRepository
     * @param TokenGenerator             $generator
     * @param UserFactory                $userFactory
     * @param IdentificationDataStorage  $identificationDataStorage
     */
    public function __construct(
        CommonFlowHandler $commonFlowHandler,
        UserRepository $userRepository,
        SubscriptionFactory $subscriptionCreator,
        SubscriptionRepository $subscriptionRepository,
        SubscriptionPackRepository $subscriptionPackRepository,
        EntitySaveHelper $entitySaveHelper,
        CarrierRepositoryInterface $carrierRepository,
        TokenGenerator $generator,
        UserFactory $userFactory,
        IdentificationDataStorage $identificationDataStorage
    )
    {
        $this->commonFlowHandler          = $commonFlowHandler;
        $this->userRepository             = $userRepository;
        $this->subscriptionCreator        = $subscriptionCreator;
        $this->subscriptionRepository     = $subscriptionRepository;
        $this->subscriptionPackRepository = $subscriptionPackRepository;
        $this->entitySaveHelper           = $entitySaveHelper;
        $this->carrierRepository          = $carrierRepository;
        $this->generator                  = $generator;
        $this->userFactory                = $userFactory;
        $this->identificationDataStorage  = $identificationDataStorage;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ID::HUTCH_INDONESIA;
    }

    /**
     * @param Request $request
     * @param string  $type
     *
     * @throws \Exception
     */
    public function process(Request $request, string $type)
    {
        $requestParams    = (Object)$request->request->all();
        $billingCarrierId = $requestParams->carrier;
        $carrier          = $this->carrierRepository->findOneByBillingId($billingCarrierId);
        $user             = $this->userRepository->findOneByMsisdn($requestParams->provider_user);

        if (!$user) {
            $newToken = $this->generator->generateToken();
            $user     = $this->userFactory->create(
                $requestParams->provider_user,
                $carrier,
                $request->getClientIp(),
                $newToken
            );

            $this->identificationDataStorage->setIdentificationToken($newToken);
        }
        /** @var Subscription $subscription */
        $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

        if (!$subscription) {
            /** @var SubscriptionPack $subscriptionPack */
            $subscriptionPack = $this->subscriptionPackRepository->findOneBy(['carrier' => $carrier, 'status' => 1]);

            $subscription = $this->subscriptionCreator->create($user, $subscriptionPack);
            $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
            $this->entitySaveHelper->persistAndSave($user);
            $this->entitySaveHelper->persistAndSave($subscription);
        }

        $this->commonFlowHandler->process($request, ID::HUTCH_INDONESIA, $type);
    }
}