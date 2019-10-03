<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 15:08
 */

namespace SubscriptionBundle\Subscription\Unsubscribe;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\Exception\NotificationSendFailedException;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\UnsubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\FakeResponseProvider;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\Common\SendUnsubscribeNotificationPerformer;

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
     * @var SendUnsubscribeNotificationPerformer
     */
    private $sendUnsubscribeNotificationPerformer;
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * Unsubscriber constructor.
     *
     * @param EntitySaveHelper $entitySaveHelper
     * @param FakeResponseProvider $fakeResponseProvider
     * @param Notifier $notifier
     * @param UnsubscribeProcess $unsubscribeProcess
     * @param OnUnsubscribeUpdater $onUnsubscribeUpdater
     * @param UnsubscribeParametersProvider $parametersProvider
     * @param UnsubscribeEventChecker $unsubscribeEventChecker
     * @param UnsubscribeEventTracker $unsubscribeEventTracker
     * @param SendUnsubscribeNotificationPerformer $sendUnsubscribeNotificationPerformer
     * @param ProcessResultSuccessChecker $resultSuccessChecker
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntitySaveHelper $entitySaveHelper,
        FakeResponseProvider $fakeResponseProvider,
        Notifier $notifier,
        UnsubscribeProcess $unsubscribeProcess,
        OnUnsubscribeUpdater $onUnsubscribeUpdater,
        UnsubscribeParametersProvider $parametersProvider,
        UnsubscribeEventChecker $unsubscribeEventChecker,
        UnsubscribeEventTracker $unsubscribeEventTracker,
        SendUnsubscribeNotificationPerformer $sendUnsubscribeNotificationPerformer,
        ProcessResultSuccessChecker $resultSuccessChecker,
        LoggerInterface $logger
    )
    {
        $this->entitySaveHelper                     = $entitySaveHelper;
        $this->fakeResponseProvider                 = $fakeResponseProvider;
        $this->notifier                             = $notifier;
        $this->unsubscribeProcess                   = $unsubscribeProcess;
        $this->onUnsubscribeUpdater                 = $onUnsubscribeUpdater;
        $this->parametersProvider                   = $parametersProvider;
        $this->unsubscribeEventChecker              = $unsubscribeEventChecker;
        $this->unsubscribeEventTracker              = $unsubscribeEventTracker;
        $this->sendUnsubscribeNotificationPerformer = $sendUnsubscribeNotificationPerformer;
        $this->resultSuccessChecker                 = $resultSuccessChecker;
        $this->logger                               = $logger;
    }

    /**
     * @param Subscription $subscription
     * @param SubscriptionPack $subscriptionPack
     * @param array $additionalParameters
     * @return ProcessResult
     * @throws \SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException
     */
    public function unsubscribe(
        Subscription $subscription,
        SubscriptionPack $subscriptionPack,
        array $additionalParameters = []
    ): ProcessResult
    {
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_UNSUBSCRIBE);
        $this->entitySaveHelper->persistAndSave($subscription);


        $parameters = $this->parametersProvider->provideParameters($subscription, $additionalParameters);
        if ($subscriptionPack->isFirstSubscriptionPeriodIsFree()) {
            $parameters->additionalData = array_merge($parameters->additionalData, ['isTrial' => true]);
        }

        try {
            $response = $this->unsubscribeProcess->doUnsubscribe($parameters);
            if($this->resultSuccessChecker->isSuccessful($response)) {
                $this->sendUnsubscribeNotificationPerformer->doSentNotification($subscription);
            }
            $this->onUnsubscribeUpdater->updateSubscriptionByResponse($subscription, $response);

            return $response;

        } catch (UnsubscribingProcessException $exception) {
            $this->logger->debug('Unsubscribe error', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]);
            $subscription->setStatus(Subscription::IS_ERROR);
            $subscription->setError('unsubscribing_process_exception');
            throw $exception;
        } finally {
            $this->entitySaveHelper->persistAndSave($subscription);
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