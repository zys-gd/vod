<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 15:08
 */

namespace SubscriptionBundle\Subscription\Unsubscribe;


use SubscriptionBundle\BillingFramework\Notification\API\Exception\NotificationSendFailedException;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\UnsubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\FakeResponseProvider;
use SubscriptionBundle\Subscription\Notification\Notifier;

class Unsubscriber
{
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var FakeResponseProvider
     */
    private $fakeResponseProvider;
    /**
     * @var \SubscriptionBundle\Subscription\Notification\Notifier
     */
    private $notifier;
    /**
     * @var UnsubscribeProcess
     */
    private $unsubscribeProcess;
    /**
     * @var OnUnsubscribeUpdater
     */
    private $onUnsubscribeUpdater;

    /**
     * @var UnsubscribeParametersProvider
     */
    private $parametersProvider;
    /**
     * @var UnsubscribeEventChecker
     */
    private $unsubscribeEventChecker;
    /**
     * @var UnsubscribeEventTracker
     */
    private $unsubscribeEventTracker;



    /**
     * Unsubscriber constructor.
     * @param EntitySaveHelper              $entitySaveHelper
     * @param FakeResponseProvider          $fakeResponseProvider
     * @param Notifier                      $notifier
     * @param UnsubscribeProcess            $unsubscribeProcess
     * @param OnUnsubscribeUpdater          $onUnsubscribeUpdater
     * @param UnsubscribeParametersProvider $parametersProvider
     * @param UnsubscribeEventChecker       $unsubscribeEventChecker
     * @param UnsubscribeEventTracker       $unsubscribeEventTracker
     */
    public function __construct(
        EntitySaveHelper $entitySaveHelper,
        FakeResponseProvider $fakeResponseProvider,
        Notifier $notifier,
        UnsubscribeProcess $unsubscribeProcess,
        OnUnsubscribeUpdater $onUnsubscribeUpdater,
        UnsubscribeParametersProvider $parametersProvider,
        UnsubscribeEventChecker $unsubscribeEventChecker,
        UnsubscribeEventTracker $unsubscribeEventTracker
    )
    {
        $this->entitySaveHelper            = $entitySaveHelper;
        $this->fakeResponseProvider        = $fakeResponseProvider;
        $this->notifier                    = $notifier;
        $this->unsubscribeProcess          = $unsubscribeProcess;
        $this->onUnsubscribeUpdater        = $onUnsubscribeUpdater;
        $this->parametersProvider          = $parametersProvider;
        $this->unsubscribeEventChecker     = $unsubscribeEventChecker;
        $this->unsubscribeEventTracker     = $unsubscribeEventTracker;
    }
    public function unsubscribe(
        Subscription $subscription,
        SubscriptionPack $subscriptionPack,
        array $additionalParameters = []
    )
    {
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_UNSUBSCRIBE);
        $this->entitySaveHelper->persistAndSave($subscription);

        if (!$subscriptionPack->isProviderManagedSubscriptions()) {

            $previousStatus = $subscription->getStatus();
            $previousStage  = $subscription->getCurrentStage();
            $response       = $this->fakeResponseProvider->getDummyResult(
                $subscription,
                UnsubscribeProcess::PROCESS_METHOD_UNSUBSCRIBE
            );

            try {
                $this->notifier->sendNotification(
                    UnsubscribeProcess::PROCESS_METHOD_UNSUBSCRIBE,
                    $subscription,
                    $subscriptionPack,
                    $subscription->getUser()->getCarrier()
                );
                $this->onUnsubscribeUpdater->updateSubscriptionByResponse($subscription, $response);

                $user = $subscription->getUser();

                return $response;

            } catch (NotificationSendFailedException $exception) {
                $subscription->setStatus($previousStatus);
                $subscription->setCurrentStage($previousStage);
                throw $exception;
            } finally {
                $this->entitySaveHelper->persistAndSave($subscription);
            }

        } else {
            $parameters = $this->parametersProvider->provideParameters($subscription, $additionalParameters);

            try {
                $response = $this->unsubscribeProcess->doUnsubscribe($parameters);
                $this->onUnsubscribeUpdater->updateSubscriptionByResponse($subscription, $response);

                return $response;

            } catch (UnsubscribingProcessException $exception) {
                $subscription->setStatus(Subscription::IS_ERROR);
                $subscription->setError('unsubscribing_process_exception');
                throw $exception;
            } finally {
                $this->entitySaveHelper->persistAndSave($subscription);
            }
        }
    }

    /**
     * @param Subscription $subscription
     * @param              $response
     */
    public function trackEventsForUnsubscribe(Subscription $subscription, ProcessResult $response): void
    {
        if (!$this->unsubscribeEventChecker->isNeedToBeTracked($response)) {
            return;
        }

        $this->unsubscribeEventTracker->trackUnsubscribe(
            $subscription->getUser(),
            $subscription,
            $response
        );
    }


}