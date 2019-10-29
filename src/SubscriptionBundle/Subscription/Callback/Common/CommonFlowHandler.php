<?php

namespace SubscriptionBundle\Subscription\Callback\Common;

use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\DataMapper\ConversionEventMapper;
use SubscriptionBundle\Piwik\EventPublisher;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Common\Handler\CallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Common\Handler\RenewCallbackHandler;
use SubscriptionBundle\Subscription\Callback\Common\Handler\SubscriptionCallbackHandler;
use SubscriptionBundle\Subscription\Callback\Common\Handler\UnsubscriptionCallbackHandler;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerProvider;
use SubscriptionBundle\Subscription\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomTrackingRules;
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
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    /**
     * @var AffiliateSender
     */
    private $affiliateSender;


    /**
     * @var UserInfoMapper
     */
    private $infoMapper;

    /**
     * @var CarrierCallbackHandlerProvider
     */
    private $carrierCallbackHandlerProvider;
    /**
     * @var ConversionEventMapper
     */
    private $conversionEventMapper;
    /**
     * @var EventPublisher
     */
    private $conversionEventPublisher;


    /**
     * MainHandler constructor.
     * @param LoggerInterface                $logger
     * @param CallbackTypeHandlerProvider    $callbackTypeHandlerProvider
     * @param ProcessResponseMapper          $billingFrameworkProcessResponseMapper
     * @param SubscriptionRepository         $subscriptionRepository
     * @param EntitySaveHelper               $entitySaveHelper
     * @param AffiliateSender                $affiliateService
     * @param UserInfoMapper                 $infoMapper
     * @param CarrierCallbackHandlerProvider $carrierCallbackHandlerProvider
     * @param ConversionEventMapper          $conversionEventMapper
     * @param EventPublisher                 $conversionEventPublisher
     */
    public function __construct(
        LoggerInterface $logger,
        CallbackTypeHandlerProvider $callbackTypeHandlerProvider,
        ProcessResponseMapper $billingFrameworkProcessResponseMapper,
        SubscriptionRepository $subscriptionRepository,
        EntitySaveHelper $entitySaveHelper,
        AffiliateSender $affiliateService,
        UserInfoMapper $infoMapper,
        CarrierCallbackHandlerProvider $carrierCallbackHandlerProvider,
        ConversionEventMapper $conversionEventMapper,
        EventPublisher $conversionEventPublisher
    )
    {
        $this->logger                         = $logger;
        $this->callbackTypeHandlerProvider    = $callbackTypeHandlerProvider;
        $this->processResponseMapper          = $billingFrameworkProcessResponseMapper;
        $this->subscriptionRepository         = $subscriptionRepository;
        $this->entitySaveHelper               = $entitySaveHelper;
        $this->affiliateSender                = $affiliateService;
        $this->infoMapper                     = $infoMapper;
        $this->carrierCallbackHandlerProvider = $carrierCallbackHandlerProvider;
        $this->conversionEventMapper          = $conversionEventMapper;
        $this->conversionEventPublisher       = $conversionEventPublisher;
    }

    public function process(Request $request, string $carrierId, string $type): Subscription
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
            throw new \Exception("There is no such subscription");
        }

        if ($carrierId != $subscription->getSubscriptionPack()->getCarrier()->getBillingCarrierId()) {
            throw new \Exception("Selected carrier does not fit to selected subscription");
        }

        if (!$callbackTypeHandler->isActionAllowed($subscription)) {
            throw new \Exception("Action is not allowed for subscription");
        }

        $this->logger->debug(
            'Callback subscription before update',
            ["subscription" => $subscription]
        );

        /** @var  CallbackHandlerInterface $callbackTypeHandler */
        $callbackTypeHandler->doProcess($subscription, $processResponse);
        $this->logger->debug(
            'Successfully updated',
            ["subscription" => $subscription, "response" => $processResponse]
        );

        $carrierHandler->afterProcess($subscription, $subscription->getUser(), $processResponse);
        $callbackTypeHandler->afterProcess($subscription, $processResponse);
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
                $this->affiliateSender->checkAffiliateEligibilityAndSendEvent($subscription, $userInfo);
            }
            $event = $this->conversionEventMapper->map(
                $callbackTypeHandler->getPiwikEventName(),
                $processResponse,
                $subscription->getUser(),
                $subscription
            );
            $this->conversionEventPublisher->publish($event);
        } else {
            $carrier = $subscription->getUser()->getCarrier();
            $this->logger->debug('Event should be already tracked. Ignoring', [
                'event'   => $callbackTypeHandler->getPiwikEventName(),
                'carrier' => $carrier->getBillingCarrierId()
            ]);
        }

        return $subscription;
    }
}