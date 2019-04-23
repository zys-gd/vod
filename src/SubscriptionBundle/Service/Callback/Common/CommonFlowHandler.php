<?php

namespace SubscriptionBundle\Service\Callback\Common;

use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Exception\SubscriptionException;
use SubscriptionBundle\Piwik\SubscriptionStatisticSender;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\Callback\Common\Type\RenewCallbackHandler;
use SubscriptionBundle\Service\Callback\Common\Type\SubscriptionCallbackHandler;
use SubscriptionBundle\Service\Callback\Common\Type\UnsubscriptionCallbackHandler;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerProvider;
use SubscriptionBundle\Service\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Service\Callback\Impl\HasCustomTrackingRules;
use SubscriptionBundle\Service\EntitySaveHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommonFlowHandler
 */
class CommonFlowHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CallbackTypeHandlerProvider
     */
    private $callbackTypeHandlerProvider;

    /**
     * @var ProcessResponseMapper
     */
    private $processResponseMapper;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var UserRepository
     */
    private $UserRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    /**
     * @var AffiliateSender
     */
    private $affiliateService;

    /**
     * @var SubscriptionStatisticSender
     */
    private $subscriptionStatisticSender;

    /**
     * @var UserInfoMapper
     */
    private $infoMapper;

    /**
     * @var CarrierCallbackHandlerProvider
     */
    private $carrierCallbackHandlerProvider;

    /**
     * MainHandler constructor.
     * @param LoggerInterface                $logger
     * @param CallbackTypeHandlerProvider    $callbackTypeHandlerProvider
     * @param ProcessResponseMapper          $billingFrameworkProcessResponseMapper
     * @param SubscriptionRepository         $subscriptionRepository
     * @param UserRepository                 $UserRepository
     * @param EventDispatcherInterface       $eventDispatcher
     * @param EntitySaveHelper               $entitySaveHelper
     * @param AffiliateSender                $affiliateService
     * @param SubscriptionStatisticSender    $subscriptionStatisticSender
     * @param UserInfoMapper                 $infoMapper
     * @param CarrierCallbackHandlerProvider $carrierCallbackHandlerProvider
     */
    public function __construct(
        LoggerInterface $logger,
        CallbackTypeHandlerProvider $callbackTypeHandlerProvider,
        ProcessResponseMapper $billingFrameworkProcessResponseMapper,
        SubscriptionRepository $subscriptionRepository,
        UserRepository $UserRepository,
        EventDispatcherInterface $eventDispatcher,
        EntitySaveHelper $entitySaveHelper,
        AffiliateSender $affiliateService,
        SubscriptionStatisticSender $subscriptionStatisticSender,
        UserInfoMapper $infoMapper,
        CarrierCallbackHandlerProvider $carrierCallbackHandlerProvider
    )
    {
        $this->logger                         = $logger;
        $this->callbackTypeHandlerProvider    = $callbackTypeHandlerProvider;
        $this->processResponseMapper          = $billingFrameworkProcessResponseMapper;
        $this->subscriptionRepository         = $subscriptionRepository;
        $this->UserRepository                 = $UserRepository;
        $this->eventDispatcher                = $eventDispatcher;
        $this->entitySaveHelper               = $entitySaveHelper;
        $this->affiliateService               = $affiliateService;
        $this->subscriptionStatisticSender    = $subscriptionStatisticSender;
        $this->infoMapper                     = $infoMapper;
        $this->carrierCallbackHandlerProvider = $carrierCallbackHandlerProvider;
    }


    public function process(Request $request, string $carrierId, string $type)
    {
        $requestParams = (Object)$request->request->all();

        $this->logger->debug("Callback received for {$type}");
        /** @var HasCommonFlow $carrierHandler */
        $callbackTypeHandler = $this->callbackTypeHandlerProvider->getHandler($type);
        $carrierHandler      = $this->carrierCallbackHandlerProvider->getHandler($carrierId, $request, $type);


        $this->logger->debug(
            "Callback raw response  for {$type}",
            ["response" => $requestParams, "type" => $type]
        );

        $processResponse = $this->processResponseMapper->map($type, (object)['data' => $requestParams]);

        $this->logger->debug(
            "Callback prepared response for {$type}",
            ["response" => $processResponse, "type" => $type]
        );

        $subscriptionId = $processResponse->getClientId();
        if ($subscriptionId) {
            $subscription = $this->subscriptionRepository->findOneBy(['uuid' => $subscriptionId]);
        } else {

            /** @var User $User */
            $user = $carrierHandler->getUser($requestParams->provider_user);
            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);
        }

        if (!$subscription instanceof Subscription) {
            throw new SubscriptionException("There is no such subscription");
        }

        if ($carrierId != $subscription->getSubscriptionPack()->getBillingCarrierId()) {
            throw new SubscriptionException("Selected carrier does not fit to selected subscription");
        }

        $this->logger->debug(
            'Callback subscription before update',
            ["subscription" => $subscription]
        );

        /** @var  RenewCallbackHandler|SubscriptionCallbackHandler|UnsubscriptionCallbackHandler $callbackTypeHandler */
        $callbackTypeHandler->updateSubscriptionByCallbackData($subscription, $processResponse);
        $this->logger->debug(
            'Successfully updated',
            ["subscription" => $subscription, "response" => $processResponse]
        );

        $carrierHandler->afterProcess($subscription, $subscription->getUser(), $processResponse);
        $this->entitySaveHelper->persistAndSave($subscription);

        if ($carrierHandler instanceof HasCustomTrackingRules) {
            $isNeedToBeTracked = $carrierHandler->isNeedToBeTracked($processResponse);
        } else {
            // TODO We have no such check on `subscribe method`.
            // TODO its better to move all these sends to callback
            // TODO refactor candidate
            // TODO also im lazy. :P

            $isNeedToBeTracked = ($type !== 'subscribe');
        }

        if ($isNeedToBeTracked) {
            $userInfo = $this->infoMapper->mapFromUser($subscription->getUser());
            if ($type === 'subscribe') {
                $this->affiliateService->checkAffiliateEligibilityAndSendEvent($subscription, $userInfo);
            }
            $this->subscriptionStatisticSender->send(
                $callbackTypeHandler->getPiwikEventName(),
                $subscription->getUser(),
                $subscription,
                $processResponse
            );
        } else {
            $carrier = $subscription->getUser()->getCarrier();
            $this->logger->info('Event should be already tracked. Ignoring', [
                'event'   => $callbackTypeHandler->getPiwikEventName(),
                'carrier' => $carrier->getBillingCarrierId()
            ]);
        }
    }

}