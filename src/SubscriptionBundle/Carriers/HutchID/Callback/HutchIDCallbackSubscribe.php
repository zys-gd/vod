<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\User\Service\UserFactory;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Carriers\HutchID\Subscribe\HutchIDSMSSubscriber;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomFlow;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomTrackingRules;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Subscribe\Common\SubscriptionEventTracker;
use SubscriptionBundle\Subscription\Subscribe\Exception\SubscriptionFlowException;
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
     * @var ProcessResponseMapper
     */
    private $processResponseMapper;
    /**
     * @var HutchIDSMSSubscriber
     */
    private $hutchIDSMSSubscriber;

    /**
     * @var Notifier
     */
    private $notifier;
    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;

    /**
     * HutchIDCallbackSubscribe constructor.
     *
     * @param CommonFlowHandler           $commonFlowHandler
     * @param UserRepository              $userRepository
     * @param SubscriptionFactory         $subscriptionCreator
     * @param SubscriptionRepository      $subscriptionRepository
     * @param SubscriptionPackRepository  $subscriptionPackRepository
     * @param EntitySaveHelper            $entitySaveHelper
     * @param CarrierRepositoryInterface  $carrierRepository
     * @param TokenGenerator              $generator
     * @param UserFactory                 $userFactory
     * @param IdentificationDataStorage   $identificationDataStorage
     * @param ProcessResponseMapper       $processResponseMapper
     * @param HutchIDSMSSubscriber        $hutchIDSMSSubscriber
     * @param Notifier                    $notifier
     * @param SubscriptionEventTracker    $subscriptionEventTracker
     * @param LoggerInterface             $logger
     * @param ProcessResultSuccessChecker $processResultSuccessChecker
     * @param ApiConnector                $apiConnector
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
        IdentificationDataStorage $identificationDataStorage,
        ProcessResponseMapper $processResponseMapper,
        HutchIDSMSSubscriber $hutchIDSMSSubscriber,
        Notifier $notifier,
        SubscriptionEventTracker $subscriptionEventTracker,
        LoggerInterface $logger,
        ProcessResultSuccessChecker $processResultSuccessChecker,
        ApiConnector $apiConnector
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
        $this->processResponseMapper      = $processResponseMapper;
        $this->hutchIDSMSSubscriber       = $hutchIDSMSSubscriber;
        $this->notifier                   = $notifier;
        $this->subscriptionEventTracker   = $subscriptionEventTracker;
        $this->logger                     = $logger;
        $this->resultSuccessChecker       = $processResultSuccessChecker;
        $this->crossSubscriptionApi       = $apiConnector;
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
        $requestParams   = (Object)$request->request->all();
        $processResponse = $this->processResponseMapper->map($type, (object)['data' => $requestParams]);

        try {
            if ($requestParams->provider_fields['source'] != 'SMS') {
                $this->logger->info('Hutch ID listen callback source is not SMS');
                throw new SubscriptionFlowException();
            }

            $billingCarrierId = $requestParams->carrier;
            $carrier          = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            $user             = $this->userRepository->findOneByMsisdn($requestParams->provider_user);

            if (!$user) {
                $newToken = $this->generator->generateToken();
                $user     = $this->userFactory->create(
                    $requestParams->provider_user,
                    $carrier,
                    '',
                    $newToken,
                    $processResponse->getId()
                );

                $this->entitySaveHelper->persistAndSave($user);

                $this->identificationDataStorage->setIdentificationToken($newToken);
            }

            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

            if (!$subscription) {
                /** @var SubscriptionPack $subscriptionPack */
                $subscriptionPack = $this->subscriptionPackRepository->findOneBy(['carrier' => $carrier, 'status' => 1]);

                $subscription = $this->subscriptionCreator->create($user, $subscriptionPack);
            }

            $this->hutchIDSMSSubscriber->subscribe($subscription, $processResponse);
            $this->entitySaveHelper->persistAndSave($subscription);

            // send SMS
            if ($subscription->isSubscribed() && $this->resultSuccessChecker->isSuccessful($processResponse)) {
                $this->logger->debug('Hutch ID listen callback created successful subscription. Start tracking');
                $this->notifier->sendNotification(
                    SubscribeProcess::PROCESS_METHOD_SUBSCRIBE,
                    $subscription,
                    $subscription->getSubscriptionPack(),
                    $carrier
                );

                // track event
                // TODO: use afterSubscriptionProcessTracker?
                $isNeedToBeTracked = true;
                if ($this instanceof HasCustomTrackingRules) {
                    $isNeedToBeTracked = $this->isNeedToBeTracked($processResponse);
                }
                if ($isNeedToBeTracked) {
                    $this->subscriptionEventTracker->trackSubscribe($subscription, $processResponse);
                }

                $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
            }

            $this->logger->debug('Hutch ID listen callback created subscription', [$subscription]);
            return $subscription;

        } catch (SubscriptionFlowException $e) {
            $this->logger->info('Hutch ID listen callback through common flow');
            return $this->commonFlowHandler->process($request, ID::HUTCH_INDONESIA, $type);
        }
    }
}