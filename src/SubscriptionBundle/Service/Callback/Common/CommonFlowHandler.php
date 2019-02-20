<?php
/**
 * Created by PhpStorm.
 * User: Maxim Nevstruev
 * Date: 23.02.2018
 * Time: 11:46
 */

namespace SubscriptionBundle\Service\Callback\Common;


use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
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
use IdentificationBundle\Entity\User;

class CommonFlowHandler
{
    private $logger;
    private $callbackTypeHandlerProvider;
    private $processResponseMapper;
    private $subscriptionRepository;
    private $UserRepository;
    private $eventDispatcher;
    private $entitySaveHelper;
    private $affiliateService;
    private $piwikStatisticSender;
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
     * @param PiwikStatisticSender           $piwikStatisticSender
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
        PiwikStatisticSender $piwikStatisticSender,
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
            /** @var User $User */
            $User = $this->UserRepository->findOneBy(['identifier' => $requestParams->provider_user]);
            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->findOneBy(['owner' => $User]);
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
            $this->piwikStatisticSender->trackWithBillingResponse(
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