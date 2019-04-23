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
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\BillingFramework\Notification\API\Exception\NotificationSendFailedException;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\Action\Common\FakeResponseProvider;
use SubscriptionBundle\Service\Action\Common\PromotionalResponseChecker;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\Notification\Notifier;
use SubscriptionBundle\Service\SubscriptionCreator;
use SubscriptionBundle\Service\SubscriptionLimiter\SubscriptionLimitCompleter;
use SubscriptionBundle\Service\SubscriptionSerializer;
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
     * @var SubscribeParametersProvider
     */
    private $subscribeParametersProvider;
    /**
     * @var SubscriptionLimitCompleter
     */
    private $subscriptionLimitCompleter;
    /**
     * @var SubscriptionSerializer
     */
    private $subscriptionSerializer;


    /**
     * Subscriber constructor.
     *
     * @param LoggerInterface             $logger
     * @param EntitySaveHelper            $entitySaveHelper
     * @param SessionInterface            $session
     * @param SubscriptionCreator         $subscriptionCreator
     * @param PromotionalResponseChecker  $promotionalResponseChecker
     * @param FakeResponseProvider        $fakeResponseProvider
     * @param Notifier                    $notifier
     * @param SubscribeProcess            $subscribeProcess
     * @param OnSubscribeUpdater          $onSubscribeUpdater
     * @param SubscribeParametersProvider $subscribeParametersProvider
     * @param SubscriptionLimitCompleter  $subscriptionLimitCompleter
     * @param SubscriptionSerializer      $subscriptionSerializer
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
        SubscribeParametersProvider $subscribeParametersProvider,
        SubscriptionLimitCompleter $subscriptionLimitCompleter,
        SubscriptionSerializer $subscriptionSerializer
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
        $this->subscribeParametersProvider = $subscribeParametersProvider;
        $this->subscriptionLimitCompleter  = $subscriptionLimitCompleter;
        $this->subscriptionSerializer      = $subscriptionSerializer;
    }

    /**
     * Subscribe user to given subscription pack
     *
     * @param User             $user
     * @param SubscriptionPack $plan
     * @param array            $additionalData
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function subscribe(User $user, SubscriptionPack $plan, $additionalData = []): array
    {
        $var = AffiliateVisitSaver::extractPageVisitData($this->session, true);

        $this->logger->debug('Creating subscription', ['campaignData' => $var]);

        $subscription = $this->createPendingSubscription($user, $plan);
        $subscription->setAffiliateToken(json_encode($var));


        if ($subscription->getSubscriptionPack()->isFirstSubscriptionPeriodIsFree() &&
            !$subscription->getSubscriptionPack()->isProviderManagedSubscriptions()) {
            $tierIdWithZeroValue = $this->getPriceTierIdWithZeroValue($subscription->getSubscriptionPack()->getCarrier());
            $subscription->setPromotionTierId($tierIdWithZeroValue);
        }

        try {
            $response = $this->performSubscribe($additionalData, $subscription);
            $this->onSubscribeUpdater->updateSubscriptionByResponse($subscription, $response);
            $this->subscriptionLimitCompleter->finishProcess($response, $this->session);

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
     *
     * @return ProcessResult
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function resubscribe(Subscription $existingSubscription,
                                SubscriptionPack $plan,
                                $additionalData = []): ProcessResult
    {
        $subscription = $existingSubscription;

        $this->applyResubscribeTierChanges($subscription);

        try {

            $response = $this->performSubscribe($additionalData, $subscription);
            $this->onSubscribeUpdater->updateSubscriptionByResponse($subscription, $response);
            $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
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
     *
     * @return ProcessResult
     * @throws SubscribingProcessException
     */
    protected function performSubscribe(array $additionalData, Subscription $subscription): ProcessResult
    {
        if ($this->promotionalResponseChecker->isPromotionalResponseNeeded($subscription)) {

            $carrier = $subscription->getUser()->getCarrier();

            try {
                $this->notifier->sendNotification(
                    SubscribeProcess::PROCESS_METHOD_SUBSCRIBE,
                    $subscription,
                    $subscription->getSubscriptionPack(),
                    $carrier
                );

            } catch (NotificationSendFailedException $e) {

                $this->logger->error($e->getMessage(), [
                    'subscription' => $this->subscriptionSerializer->serializeShort($subscription)
                ]);

                throw new SubscribingProcessException('Error while trying to subscribe', 0, $e);
            }

            return $this->fakeResponseProvider->getDummyResult(
                $subscription,
                SubscribeProcess::PROCESS_METHOD_SUBSCRIBE,
                ProcessResult::STATUS_SUCCESSFUL
            );

        } else {
            $parameters = $this->subscribeParametersProvider->provideParameters($subscription, $additionalData);
            return $this->subscribeProcess->doSubscribe($parameters);
        }
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
     *
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

}