<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
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
     * @var SubscriptionCallbackHandler
     */
    private $subscriptionCallbackHandler;
    /**
     * @var UserInfoMapper
     */
    private $infoMapper;
    /**
     * @var AffiliateSender
     */
    private $affiliateSender;
    /**
     * @var ConversionEventMapper
     */
    private $conversionEventMapper;
    /**
     * @var EventPublisher
     */
    private $conversionEventPublisher;
    /**
     * @var Notifier
     */
    private $notifier;

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
     * @param SubscriptionCallbackHandler $subscriptionCallbackHandler
     * @param UserInfoMapper              $infoMapper
     * @param AffiliateSender             $affiliateSender
     * @param ConversionEventMapper       $conversionEventMapper
     * @param EventPublisher              $conversionEventPublisher
     * @param Notifier                    $notifier
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
        SubscriptionCallbackHandler $subscriptionCallbackHandler,
        UserInfoMapper $infoMapper,
        AffiliateSender $affiliateSender,
        ConversionEventMapper $conversionEventMapper,
        EventPublisher $conversionEventPublisher,
        Notifier $notifier
    )
    {
        $this->commonFlowHandler           = $commonFlowHandler;
        $this->userRepository              = $userRepository;
        $this->subscriptionCreator         = $subscriptionCreator;
        $this->subscriptionRepository      = $subscriptionRepository;
        $this->subscriptionPackRepository  = $subscriptionPackRepository;
        $this->entitySaveHelper            = $entitySaveHelper;
        $this->carrierRepository           = $carrierRepository;
        $this->generator                   = $generator;
        $this->userFactory                 = $userFactory;
        $this->identificationDataStorage   = $identificationDataStorage;
        $this->processResponseMapper       = $processResponseMapper;
        $this->hutchIDSMSSubscriber        = $hutchIDSMSSubscriber;
        $this->subscriptionCallbackHandler = $subscriptionCallbackHandler;
        $this->infoMapper                  = $infoMapper;
        $this->affiliateSender             = $affiliateSender;
        $this->conversionEventMapper       = $conversionEventMapper;
        $this->conversionEventPublisher    = $conversionEventPublisher;
        $this->notifier                    = $notifier;
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

        try {
            if ($requestParams->provider_fields['source'] != 'SMS') {
                throw new \Exception();
            }

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

                $this->entitySaveHelper->persistAndSave($user);

                $this->identificationDataStorage->setIdentificationToken($newToken);
            }

            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

            if (!$subscription) {
                /** @var SubscriptionPack $subscriptionPack */
                $subscriptionPack = $this->subscriptionPackRepository->findOneBy(['carrier' => $carrier, 'status' => 1]);

                $subscription = $this->subscriptionCreator->create($user, $subscriptionPack);
                $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
            }

            $processResponse = $this->processResponseMapper->map($type, (object)['data' => $requestParams]);
            $this->hutchIDSMSSubscriber->subscribe($subscription, $processResponse);
            $this->entitySaveHelper->persistAndSave($subscription);

            // send SMS
            $this->notifier->sendNotification(
                SubscribeProcess::PROCESS_METHOD_SUBSCRIBE,
                $subscription,
                $subscription->getSubscriptionPack(),
                $carrier
            );

            // track event
            if ($this instanceof HasCustomTrackingRules) {
                $isNeedToBeTracked = $this->isNeedToBeTracked($processResponse);
            }
            else {
                $isNeedToBeTracked = ($type !== 'subscribe');
            }

            if ($isNeedToBeTracked) {
                $userInfo = $this->infoMapper->mapFromUser($subscription->getUser());
                if ($type === 'subscribe') {
                    $this->affiliateSender->checkAffiliateEligibilityAndSendEvent($subscription, $userInfo);
                }
                $event = $this->conversionEventMapper->map(
                    $this->subscriptionCallbackHandler->getPiwikEventName(),
                    $processResponse,
                    $subscription->getUser(),
                    $subscription
                );
                $this->conversionEventPublisher->publish($event);
            }

            return $subscription;

        } catch (\Throwable $e) {
            return $this->commonFlowHandler->process($request, ID::HUTCH_INDONESIA, $type);
        }
    }
}