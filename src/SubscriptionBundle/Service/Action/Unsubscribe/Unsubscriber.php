<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 15:08
 */

namespace SubscriptionBundle\Service\Action\Unsubscribe;


use AffiliateBundle\Service\CarrierTrackingTypeChecker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\UnsubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Piwik\PiwikStatisticSender;
use SubscriptionBundle\Service\Action\Common\FakeResponseProvider;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\Notification\Notifier;

class Unsubscriber
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
     * @var FakeResponseProvider
     */
    private $fakeResponseProvider;
    /**
     * @var \SubscriptionBundle\Service\Notification\Notifier
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
     * @var CarrierTrackingTypeChecker
     */
    private $trackingTypeChecker;
    /**
     * @var PiwikStatisticSender
     */
    private $piwikStatisticSender;
    /**
     * @var UnsubscribeParametersProvider
     */
    private $parametersProvider;


    /**
     * Unsubscriber constructor.
     * @param LoggerInterface               $logger
     * @param EntitySaveHelper              $entitySaveHelper
     * @param FakeResponseProvider          $fakeResponseProvider
     * @param Notifier                      $notifier
     * @param UnsubscribeProcess            $unsubscribeProcess
     * @param OnUnsubscribeUpdater          $onUnsubscribeUpdater
     * @param CarrierTrackingTypeChecker    $trackingTypeChecker
     * @param PiwikStatisticSender          $piwikStatisticSender
     * @param UnsubscribeParametersProvider $parametersProvider
     */
    public function __construct(
        LoggerInterface $logger,
        EntitySaveHelper $entitySaveHelper,
        FakeResponseProvider $fakeResponseProvider,
        Notifier $notifier,
        UnsubscribeProcess $unsubscribeProcess,
        OnUnsubscribeUpdater $onUnsubscribeUpdater,
        CarrierTrackingTypeChecker $trackingTypeChecker,
        PiwikStatisticSender $piwikStatisticSender,
        UnsubscribeParametersProvider $parametersProvider
    )
    {
        $this->logger               = $logger;
        $this->entitySaveHelper     = $entitySaveHelper;
        $this->fakeResponseProvider = $fakeResponseProvider;
        $this->notifier             = $notifier;
        $this->unsubscribeProcess   = $unsubscribeProcess;
        $this->onUnsubscribeUpdater = $onUnsubscribeUpdater;
        $this->trackingTypeChecker  = $trackingTypeChecker;
        $this->piwikStatisticSender = $piwikStatisticSender;
        $this->parametersProvider   = $parametersProvider;
    }

    public function unsubscribe(Subscription $subscription, SubscriptionPack $subscriptionPack)
    {

        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_UNSUBSCRIBE);
        $this->entitySaveHelper->persistAndSave($subscription);

        try {
            if (!$subscriptionPack->isProviderManagedSubscriptions()) {
                $response = $this->fakeResponseProvider->getDummyResult($subscription, UnsubscribeProcess::PROCESS_METHOD_UNSUBSCRIBE);
                $this->notifier->sendNotification(
                    UnsubscribeProcess::PROCESS_METHOD_UNSUBSCRIBE,
                    $subscription,
                    $subscriptionPack,
                    $subscription->getOwner()->getCarrier()
                );
            } else {
                $parameters = $this->parametersProvider->provideParameters($subscription);
                $response   = $this->unsubscribeProcess->doUnsubscribe($parameters);
            }

            $this->onUnsubscribeUpdater->updateSubscriptionByResponse($subscription, $response);

            return $response;

        } catch (UnsubscribingProcessException $exception) {
            $subscription->setStatus(Subscription::IS_ERROR);
            throw $exception;
        } finally {
            $this->entitySaveHelper->persistAndSave($subscription);
        }
    }

    /**
     * @param Subscription $subscription
     * @param              $response
     */
    public function trackEventsForUnsubscribe(Subscription $subscription, ProcessResult $response)
    {
        $this->piwikStatisticSender->trackUnsubscribe(
            $subscription->getOwner(),
            $subscription,
            $response
        );
    }


}