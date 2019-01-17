<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 14:58
 */

namespace SubscriptionBundle\Service\Action\Subscribe;


use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @var AffiliateSender
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
     * @var SubscriptionHandlerProvider
     */
    private $subscriptionHandlerProvider;
    /**
     * @var SubscribeParametersProvider
     */
    private $subscribeParametersProvider;

    /**
     * Subscriber constructor.
     * @param LoggerInterface             $logger
     * @param EntitySaveHelper            $entitySaveHelper
     * @param SessionInterface            $session
     * @param SubscriptionCreator         $subscriptionCreator
     * @param PromotionalResponseChecker  $promotionalResponseChecker
     * @param FakeResponseProvider        $fakeResponseProvider
     * @param Notifier                    $notifier
     * @param SubscribeProcess            $subscribeProcess
     * @param OnSubscribeUpdater          $onSubscribeUpdater
     * @param AffiliateSender            $affiliateService
     * @param UserInfoMapper              $userInfoMapper
     * @param PiwikStatisticSender        $piwikStatisticSender
     * @param SubscriptionHandlerProvider $subscriptionHandlerProvider
     * @param SubscribeParametersProvider $subscribeParametersProvider
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
        AffiliateSender $affiliateService,
        UserInfoMapper $userInfoMapper,
        PiwikStatisticSender $piwikStatisticSender,
        SubscriptionHandlerProvider $subscriptionHandlerProvider,
        SubscribeParametersProvider $subscribeParametersProvider
    )
    {
        $this->logger                      = $logger;
        $this->entitySaveHelper            = $entitySaveHelper;
        $this->session                     = $session;
        $this->subscriptionCreator         = $subscriptionCreator;
        $this->promotionalResponseChecker  = $promotionalResponseChecker;
        $this->fakeResponseProvider        = $fakeResponseProvider;
        $this->notifier                    = $notifier;
        $this->subscribeProcess            = $subscribeProcess;
        $this->onSubscribeUpdater          = $onSubscribeUpdater;
        $this->affiliateService            = $affiliateService;
        $this->userInfoMapper              = $userInfoMapper;
        $this->piwikStatisticSender        = $piwikStatisticSender;
        $this->subscriptionHandlerProvider = $subscriptionHandlerProvider;
        $this->subscribeParametersProvider = $subscribeParametersProvider;
    }


    /**
     * Subscribe user to given subscription pack
     * @param User             $user
     * @param SubscriptionPack $plan
     * @param array            $additionalData
     * @return array
     */
    public function subscribe(User $user, SubscriptionPack $plan, $additionalData = []): array
    {
        $var = $this->session->get('campaignData');
        $this->logger->debug('Creating subscription', ['campaignData' => $var]);

        $subscription = $this->createPendingSubscription($user, $plan);
        $subscription->setAffiliateToken($var);


        if ($subscription->getSubscriptionPack()->isFirstSubscriptionPeriodIsFree() &&
            !$subscription->getSubscriptionPack()->isProviderManagedSubscriptions()) {
            $tierIdWithZeroValue = $this->getPriceTierIdWithZeroValue($subscription->getSubscriptionPack()->getCarrier());
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

        $User         = $existingSubscription->getUser();
        $subscription = $this->createPendingSubscription($User, $plan);

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

//TODO: remove fake
    private function getPriceTierIdWithZeroValue($carrierId)
    {
        return 0;
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

            $carrier = $subscription->getUser()->getCarrier();
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
        if ($subscriptionPack->isFirstSubscriptionPeriodIsFree() /*&&*/
            /*$subscriptionPack->isFirstSubscriptionPeriodIsFreeMultiple()*/
        ) {
            $tierIdWithZeroValue = $this->getPriceTierIdWithZeroValue($subscriptionPack->getCarrierId());
            $subscription->setPromotionTierId($tierIdWithZeroValue);
        }
    }

    /**
     * @param User             $User
     * @param SubscriptionPack $plan
     * @return Subscription
     */
    private function createPendingSubscription(User $User, SubscriptionPack $plan): Subscription
    {
        $subscription = $this->subscriptionCreator->create($User, $plan);
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
        $this->entitySaveHelper->persistAndSave($subscription);
        return $subscription;
    }

    public function trackEventsForSubscribe(Subscription $subscription, ProcessResult $response)
    {
        $this->affiliateService->checkAffiliateEligibilityAndSendEvent(
            $subscription,
            $this->userInfoMapper->mapFromUser($subscription->getUser())
        );
        $this->piwikStatisticSender->trackSubscribe(
            $subscription->getUser(),
            $subscription,
            $response
        );
    }

    public function trackEventsForResubscribe(Subscription $subscription, ProcessResult $response)
    {
        $this->affiliateService->checkAffiliateEligibilityAndSendEvent(
            $subscription,
            $this->userInfoMapper->mapFromUser($subscription->getUser())
        );
        $this->piwikStatisticSender->trackResubscribe(
            $subscription->getUser(),
            $subscription,
            $response
        );
    }
}