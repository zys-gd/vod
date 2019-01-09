<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 14:58
 */

namespace SubscriptionBundle\Service\Action\Subscribe;


use AffiliateBundle\Service\AffiliateService;
use AffiliateBundle\Service\UserInfoMapper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\BillingFramework\Process\SubscriptionPackDataProvider;
use SubscriptionBundle\Entity\Price;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Entity\SubscriptionPlanInterface;
use SubscriptionBundle\Piwik\PiwikStatisticSender;
use SubscriptionBundle\Service\Action\Common\FakeResponseProvider;
use SubscriptionBundle\Service\Action\Common\PromotionalResponseChecker;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\Notification\Notifier;
use SubscriptionBundle\Service\SubscriptionCreator;
use UserBundle\Entity\BillableUser;

class Subscriber
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var SubscriptionCreator
     */
    private $subscriptionCreator;
    /**
     * @var PromotionalResponseChecker
     */
    private $promotionalResponseChecker;
    /**
     * @var FakeResponseProvider
     */
    private $fakeResponseProvider;
    /**
     * @var Notifier
     */
    private $notifier;
    /**
     * @var SubscribeProcess
     */
    private $subscribeProcess;
    /**
     * @var OnSubscribeUpdater
     */
    private $onSubscribeUpdater;
    /**
     * @var AffiliateService
     */
    private $affiliateService;
    /**
     * @var UserInfoMapper
     */
    private $userInfoMapper;
    /**
     * @var PiwikStatisticSender
     */
    private $piwikStatisticSender;
    /**
     * @var SubscriptionPackDataProvider
     */
    private $subscriptionPackDataProvider;
    /**
     * @var SubscriptionHandlerProvider
     */
    private $subscriptionHandlerProvider;
    /**
     * @var SubscribeParametersProvider
     */
    private $subscribeParametersProvider;

    /**
     * Subscriber constructor.
     * @param LoggerInterface              $logger
     * @param EntitySaveHelper             $entitySaveHelper
     * @param SessionInterface             $session
     * @param SubscriptionCreator          $subscriptionCreator
     * @param PromotionalResponseChecker   $promotionalResponseChecker
     * @param FakeResponseProvider         $fakeResponseProvider
     * @param Notifier                     $notifier
     * @param SubscribeProcess             $subscribeProcess
     * @param OnSubscribeUpdater           $onSubscribeUpdater
     * @param AffiliateService             $affiliateService
     * @param UserInfoMapper               $userInfoMapper
     * @param PiwikStatisticSender         $piwikStatisticSender
     * @param SubscriptionPackDataProvider $subscriptionPackDataProvider
     * @param SubscriptionHandlerProvider  $subscriptionHandlerProvider
     * @param SubscribeParametersProvider  $subscribeParametersProvider
     */
    public function __construct(
        LoggerInterface $logger,
        EntitySaveHelper $entitySaveHelper,
        SessionInterface $session,
        SubscriptionCreator $subscriptionCreator,
        PromotionalResponseChecker $promotionalResponseChecker,
        FakeResponseProvider $fakeResponseProvider,
        Notifier $notifier,
        SubscribeProcess $subscribeProcess,
        OnSubscribeUpdater $onSubscribeUpdater,
        AffiliateService $affiliateService,
        UserInfoMapper $userInfoMapper,
        PiwikStatisticSender $piwikStatisticSender,
        SubscriptionPackDataProvider $subscriptionPackDataProvider,
        SubscriptionHandlerProvider $subscriptionHandlerProvider,
        SubscribeParametersProvider $subscribeParametersProvider
    )
    {
        $this->logger                       = $logger;
        $this->entitySaveHelper             = $entitySaveHelper;
        $this->session                      = $session;
        $this->subscriptionCreator          = $subscriptionCreator;
        $this->promotionalResponseChecker   = $promotionalResponseChecker;
        $this->fakeResponseProvider         = $fakeResponseProvider;
        $this->notifier                     = $notifier;
        $this->subscribeProcess             = $subscribeProcess;
        $this->onSubscribeUpdater           = $onSubscribeUpdater;
        $this->affiliateService             = $affiliateService;
        $this->userInfoMapper               = $userInfoMapper;
        $this->piwikStatisticSender         = $piwikStatisticSender;
        $this->subscriptionPackDataProvider = $subscriptionPackDataProvider;
        $this->subscriptionHandlerProvider  = $subscriptionHandlerProvider;
        $this->subscribeParametersProvider  = $subscribeParametersProvider;
    }


    /**
     * Subscribe user to given subscription pack
     * @param BillableUser              $user
     * @param SubscriptionPlanInterface $plan
     * @param array                     $additionalData
     * @return array
     */
    public function subscribe(BillableUser $user, SubscriptionPlanInterface $plan, $additionalData = []): array
    {
        $var = $this->session->get('campaignData');
        $this->logger->debug('Creating subscription', ['campaignData' => $var]);

        $subscription = $this->createPendingSubscription($user, $plan);
        $subscription->setAffiliateToken($var);


        if ($subscription->getSubscriptionPack()->isFirstSubscriptionPeriodIsFree() &&
            !$subscription->getSubscriptionPack()->isProviderManagedSubscriptions()) {
            $tierIdWithZeroValue = $this->getPriceTierIdWithZeroValue($subscription->getSubscriptionPack()->getCarrierId());
            $subscription->setPromotionTierId($tierIdWithZeroValue);
        }

        try {
            $response = $this->performSubscribe($additionalData, $subscription);
            return [$subscription, $response];

        } catch (SubscribingProcessException $exception) {
            $subscription->setStatus(Subscription::IS_ERROR);
            throw $exception;
        } finally {
            $this->entitySaveHelper->persistAndSave($subscription);
        }


    }

    /**
     * @param Subscription     $existingSubscription
     * @param SubscriptionPack $plan
     * @param array            $additionalData
     * @return ProcessResult
     * @throws \SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException
     */
    public function resubscribe(Subscription $existingSubscription, SubscriptionPack $plan, $additionalData = []): ProcessResult
    {

        $billableUser = $existingSubscription->getOwner();
        $subscription = $this->createPendingSubscription($billableUser, $plan);

        $this->applyResubscribeTierChanges($subscription);

        try {

            $response = $this->performSubscribe($additionalData, $subscription);
            $this->onSubscribeUpdater->updateSubscriptionByResponse($subscription, $response);
            return $response;

        } catch (SubscribingProcessException $exception) {
            $subscription->setStatus(Subscription::IS_ERROR);
            throw $exception;
        } finally {
            $this->entitySaveHelper->persistAndSave($subscription);
        }
    }


    private function getPriceTierIdWithZeroValue($carrierId)
    {
        $tierIdWithValueZero = PromotionalResponseChecker::MISSING_PROMOTIONAL_TIER;
        /** @var Price[] $prices */
        $prices = $this->subscriptionPackDataProvider->getTiersForCarrier($carrierId);
        if (!empty($prices) && is_array($prices)) {
            /** @var Price $price */
            foreach ($prices as $price) {
                if ($price->getValue() == 0)
                    $tierIdWithValueZero = $price->getTierId();
            }
        }
        return $tierIdWithValueZero;
    }

    /**
     * @param $additionalData
     * @param $subscription
     * @return ProcessResult
     */
    protected function performSubscribe(array $additionalData, Subscription $subscription): ProcessResult
    {
        if ($this->promotionalResponseChecker->isPromotionalResponseNeeded($subscription)) {
            $response = $this->fakeResponseProvider->getDummyResult($subscription, SubscribeProcess::PROCESS_METHOD_SUBSCRIBE);

            $carrier = $subscription->getOwner()->getCarrier();
            $this->notifier->sendNotification(
                SubscribeProcess::PROCESS_METHOD_SUBSCRIBE,
                $subscription,
                $subscription->getSubscriptionPack(),
                $carrier
            );

        } else {
            $parameters = $this->subscribeParametersProvider->provideParameters($subscription, $additionalData);
            $response   = $this->subscribeProcess->doSubscribe($parameters);
        }

        $this->onSubscribeUpdater->updateSubscriptionByResponse($subscription, $response);
        return $response;
    }

    /**
     * @param $subscription
     */
    protected function applyResubscribeTierChanges(Subscription $subscription)
    {
        $subscriptionPack = $subscription->getSubscriptionPack();
        if ($subscriptionPack->isFirstSubscriptionPeriodIsFree() &&
            $subscriptionPack->isFirstSubscriptionPeriodIsFreeMultiple()
        ) {
            $tierIdWithZeroValue = $this->getPriceTierIdWithZeroValue($subscriptionPack->getCarrierId());
            $subscription->setPromotionTierId($tierIdWithZeroValue);
        }
    }

    /**
     * @param BillableUser              $billableUser
     * @param SubscriptionPlanInterface $plan
     * @return Subscription
     */
    private function createPendingSubscription(BillableUser $billableUser, SubscriptionPlanInterface $plan): Subscription
    {
        $subscription = $this->subscriptionCreator->create($billableUser, $plan);
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
        $this->entitySaveHelper->persistAndSave($subscription);
        return $subscription;
    }

    public function trackEventsForSubscribe(Subscription $subscription, ProcessResult $response)
    {
        $this->affiliateService->checkAffiliateEligibilityAndSendEvent(
            $subscription,
            $this->userInfoMapper->mapFromBillableUser($subscription->getOwner())
        );
        $this->piwikStatisticSender->trackSubscribe(
            $subscription->getOwner(),
            $subscription,
            $response
        );
    }

    public function trackEventsForResubscribe(Subscription $subscription, ProcessResult $response)
    {
        $this->affiliateService->checkAffiliateEligibilityAndSendEvent(
            $subscription,
            $this->userInfoMapper->mapFromBillableUser($subscription->getOwner())
        );
        $this->piwikStatisticSender->trackResubscribe(
            $subscription->getOwner(),
            $subscription,
            $response
        );
    }
}