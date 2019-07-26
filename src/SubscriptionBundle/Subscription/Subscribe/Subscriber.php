<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 14:58
 */

namespace SubscriptionBundle\Subscription\Subscribe;


use IdentificationBundle\Entity\User;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Common\FakeResponseProvider;
use SubscriptionBundle\Subscription\Common\PromotionalResponseChecker;
use SubscriptionBundle\Subscription\Subscribe\Common\SubscribePerformer;
use SubscriptionBundle\Subscription\Subscribe\Common\SubscribePromotionalPerformer;
use SubscriptionBundle\Service\CapConstraint\SubscriptionCounterUpdater;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitCompleter;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
use SubscriptionBundle\Subscription\Common\SubscriptionSerializer;
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
     * @var \SubscriptionBundle\Subscription\Common\SubscriptionFactory
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
     * @var \SubscriptionBundle\Subscription\Common\SubscriptionSerializer
     */
    private $subscriptionSerializer;
    /**
     * @var SubscribePerformer
     */
    private $subscribePerformer;
    /**
     * @var SubscribePromotionalPerformer
     */
    private $subscribePromotionalPerformer;
    /**
     * @var \Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector
     */
    private $crossSubscriptionApi;


    /**
     * Subscriber constructor.
     *
     * @param LoggerInterface                                             $logger
     * @param EntitySaveHelper                                            $entitySaveHelper
     * @param SessionInterface                                            $session
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionFactory $subscriptionCreator
     * @param PromotionalResponseChecker                                  $promotionalResponseChecker
     * @param FakeResponseProvider                                        $fakeResponseProvider
     * @param Notifier                                                    $notifier
     * @param SubscribeProcess                                            $subscribeProcess
     * @param OnSubscribeUpdater                                          $onSubscribeUpdater
     * @param SubscribeParametersProvider                                    $subscribeParametersProvider
     * @param SubscriptionLimitCompleter                                     $subscriptionLimitCompleter
     * @param SubscriptionCounterUpdater                                     $subscriptionCounterUpdater
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionSerializer $subscriptionSerializer
     * @param SubscribePerformer                                             $subscribePerformer
     * @param SubscribePromotionalPerformer                                  $subscribePromotionalPerformer
     */
    public function __construct(
        LoggerInterface $logger,
        EntitySaveHelper $entitySaveHelper,
        SessionInterface $session,
        SubscriptionFactory $subscriptionCreator,
        PromotionalResponseChecker $promotionalResponseChecker,
        FakeResponseProvider $fakeResponseProvider,
        Notifier $notifier,
        SubscribeProcess $subscribeProcess,
        OnSubscribeUpdater $onSubscribeUpdater,
        SubscribeParametersProvider $subscribeParametersProvider,
        SubscriptionLimitCompleter $subscriptionLimitCompleter,
        SubscriptionSerializer $subscriptionSerializer
        ,
        SubscribePerformer $subscribePerformer,
        SubscribePromotionalPerformer $subscribePromotionalPerformer,
        ApiConnector $crossSubscriptionApi
    )
    {
        $this->logger                        = $logger;
        $this->entitySaveHelper              = $entitySaveHelper;
        $this->session                       = $session;
        $this->subscriptionCreator           = $subscriptionCreator;
        $this->promotionalResponseChecker    = $promotionalResponseChecker;
        $this->fakeResponseProvider          = $fakeResponseProvider;
        $this->notifier                      = $notifier;
        $this->subscribeProcess              = $subscribeProcess;
        $this->onSubscribeUpdater            = $onSubscribeUpdater;
        $this->subscribeParametersProvider   = $subscribeParametersProvider;
        $this->subscriptionLimitCompleter    = $subscriptionLimitCompleter;
        $this->subscriptionSerializer        = $subscriptionSerializer;
        $this->subscribePerformer            = $subscribePerformer;
        $this->subscribePromotionalPerformer = $subscribePromotionalPerformer;
        $this->crossSubscriptionApi          = $crossSubscriptionApi;
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

        try {

            if ($this->promotionalResponseChecker->isPromotionalResponseNeeded($subscription)) {
                $response = $this->subscribePromotionalPerformer->doSubscribe($subscription);
                if (!$plan->isFirstSubscriptionPeriodIsFree()) {
                    $this->subscribePerformer->doSubscribe($subscription, $additionalData);
                }
            } else {
                $response = $this->subscribePerformer->doSubscribe($subscription, $additionalData);

            }

            $this->onSubscribeUpdater->updateSubscriptionByResponse($subscription, $response);
            $this->subscriptionLimitCompleter->finishProcess($response, $subscription);
            $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());

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
     * @throws SubscribingProcessException
     */
    public function resubscribe(Subscription $existingSubscription,
                                SubscriptionPack $plan,
                                $additionalData = []): ProcessResult
    {
        $subscription = $existingSubscription;

        $this->applyResubscribeTierChanges($subscription);

        try {

            if ($this->promotionalResponseChecker->isPromotionalResponseNeeded($subscription)) {
                $response = $this->subscribePromotionalPerformer->doSubscribe($subscription);
                $this->subscribePerformer->doSubscribe($subscription, $additionalData);
            } else {
                $response = $this->subscribePerformer->doSubscribe($subscription, $additionalData);
            }


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
     * @param $subscription
     */
    protected function applyResubscribeTierChanges(Subscription $subscription)
    {
        $subscriptionPack = $subscription->getSubscriptionPack();
        if ($subscriptionPack->isFirstSubscriptionPeriodIsFree() /*&&*/
            /*$subscriptionPack->isFirstSubscriptionPeriodIsFreeMultiple()*/
        ) {
            $tierIdWithZeroValue = $this->getPriceTierIdWithZeroValue($subscriptionPack->getCarrier()->getBillingCarrierId());
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