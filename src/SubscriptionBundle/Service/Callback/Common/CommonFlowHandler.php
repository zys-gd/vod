<?php
/**
 * Created by PhpStorm.
 * User: Maxim Nevstruev
 * Date: 23.02.2018
 * Time: 11:46
 */

namespace SubscriptionBundle\Service\Callback\Common;


use AffiliateBundle\Service\AffiliateService;
use AffiliateBundle\Service\CarrierTrackingTypeChecker;
use AffiliateBundle\Service\UserInfoMapper;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Exception\SubscriptionException;
use SubscriptionBundle\Piwik\PiwikStatisticSender;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\Callback\Common\Type\RenewCallbackHandler;
use SubscriptionBundle\Service\Callback\Common\Type\SubscriptionCallbackHandler;
use SubscriptionBundle\Service\Callback\Common\Type\UnsubscriptionCallbackHandler;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerProvider;
use SubscriptionBundle\Service\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Service\Callback\Impl\HasCustomTrackingRules;
use SubscriptionBundle\Service\EntitySaveHelper;
use UserBundle\Entity\BillableUser;
use UserBundle\Repository\BillableUserRepository;

class CommonFlowHandler
{
    private $logger;
    private $callbackTypeHandlerProvider;
    private $processResponseMapper;
    private $subscriptionRepository;
    private $billableUserRepository;
    private $eventDispatcher;
    private $entitySaveHelper;
    private $carrierTrackingTypeChecker;
    private $affiliateService;
    private $piwikStatisticSender;
    /**
     * @var UserInfoMapper
     */
    private $infoMapper;
    private $apiClient;
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
     * @param BillableUserRepository         $billableUserRepository
     * @param EventDispatcherInterface       $eventDispatcher
     * @param EntitySaveHelper               $entitySaveHelper
     * @param CarrierTrackingTypeChecker     $carrierTrackingTypeChecker
     * @param AffiliateService               $affiliateService
     * @param PiwikStatisticSender           $piwikStatisticSender
     * @param UserInfoMapper                 $infoMapper
     * @param CarrierCallbackHandlerProvider $carrierCallbackHandlerProvider
     */
    public function __construct(
        LoggerInterface $logger,
        CallbackTypeHandlerProvider $callbackTypeHandlerProvider,
        ProcessResponseMapper $billingFrameworkProcessResponseMapper,
        SubscriptionRepository $subscriptionRepository,
        BillableUserRepository $billableUserRepository,
        EventDispatcherInterface $eventDispatcher,
        EntitySaveHelper $entitySaveHelper,
        CarrierTrackingTypeChecker $carrierTrackingTypeChecker,
        AffiliateService $affiliateService,
        PiwikStatisticSender $piwikStatisticSender,
        UserInfoMapper $infoMapper,
        CarrierCallbackHandlerProvider $carrierCallbackHandlerProvider
    )
    {
        $this->logger                         = $logger;
        $this->callbackTypeHandlerProvider    = $callbackTypeHandlerProvider;
        $this->processResponseMapper          = $billingFrameworkProcessResponseMapper;
        $this->subscriptionRepository         = $subscriptionRepository;
        $this->billableUserRepository         = $billableUserRepository;
        $this->eventDispatcher                = $eventDispatcher;
        $this->entitySaveHelper               = $entitySaveHelper;
        $this->carrierTrackingTypeChecker     = $carrierTrackingTypeChecker;
        $this->affiliateService               = $affiliateService;
        $this->piwikStatisticSender           = $piwikStatisticSender;
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
            $subscription = $this->subscriptionRepository->findOneBy(['id' => $subscriptionId]);
        } else {
            /** @var BillableUser $billableUser */
            $billableUser = $this->billableUserRepository->findOneBy(['identifier' => $requestParams->provider_user]);
            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->findOneBy(['owner' => $billableUser]);
        }

        if (!$subscription instanceof Subscription) {
            throw new SubscriptionException("There is no such subscription");
        }

        if ($carrierId != $subscription->getSubscriptionPack()->getCarrierId()) {
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

        $carrierHandler->afterProcess($subscription, $subscription->getOwner(), $processResponse);
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
            $userInfo = $this->infoMapper->mapFromBillableUser($subscription->getOwner());
            if ($type === 'subscribe') {
                $this->affiliateService->checkAffiliateEligibilityAndSendEvent($subscription, $userInfo);
            }
            $this->piwikStatisticSender->send(
                $callbackTypeHandler->getPiwikEventName(),
                $subscription->getOwner(),
                $subscription,
                $processResponse
            );
        } else {
            $carrier = $subscription->getOwner()->getCarrier();
            $this->logger->info('Event should be already tracked. Ignoring', [
                'event'   => $callbackTypeHandler->getPiwikEventName(),
                'carrier' => $carrier->getIdCarrier()
            ]);
        }
    }

}