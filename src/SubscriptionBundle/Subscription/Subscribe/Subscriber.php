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
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitCompleter;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\SendNotificationChecker;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\Common\SubscribePerformer;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\Common\SendNotificationPerformer;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\SubscribeProcessStarterProvider;
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
     * @var SubscriptionFactory
     */
    private $subscriptionCreator;
    /**
     * @var OnSubscribeUpdater
     */
    private $onSubscribeUpdater;
    /**
     * @var SubscriptionLimitCompleter
     */
    private $subscriptionLimitCompleter;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;
    /**
     * @var SubscribeProcessStarterProvider
     */
    private $subscribeProcessStarterProvider;

    /**
     * Subscriber constructor.
     *
     * @param LoggerInterface                 $logger
     * @param EntitySaveHelper                $entitySaveHelper
     * @param SessionInterface                $session
     * @param SubscriptionFactory             $subscriptionCreator
     * @param OnSubscribeUpdater              $onSubscribeUpdater
     * @param SubscriptionLimitCompleter      $subscriptionLimitCompleter
     * @param ApiConnector                    $crossSubscriptionApi
     * @param ProcessResultSuccessChecker     $resultSuccessChecker
     * @param SubscribeProcessStarterProvider $subscribeProcessStarterProvider
     */
    public function __construct(
        LoggerInterface $logger,
        EntitySaveHelper $entitySaveHelper,
        SessionInterface $session,
        SubscriptionFactory $subscriptionCreator,
        OnSubscribeUpdater $onSubscribeUpdater,
        SubscriptionLimitCompleter $subscriptionLimitCompleter,
        ApiConnector $crossSubscriptionApi,
        ProcessResultSuccessChecker $resultSuccessChecker,
        SubscribeProcessStarterProvider $subscribeProcessStarterProvider
    )
    {
        $this->logger                          = $logger;
        $this->entitySaveHelper                = $entitySaveHelper;
        $this->session                         = $session;
        $this->subscriptionCreator             = $subscriptionCreator;
        $this->onSubscribeUpdater              = $onSubscribeUpdater;
        $this->subscriptionLimitCompleter      = $subscriptionLimitCompleter;
        $this->crossSubscriptionApi            = $crossSubscriptionApi;
        $this->resultSuccessChecker            = $resultSuccessChecker;
        $this->subscribeProcessStarterProvider = $subscribeProcessStarterProvider;
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

            $starter  = $this->subscribeProcessStarterProvider->get($plan->getCarrier());
            $response = $starter->start($subscription, $plan, $additionalData);

            $this->onSubscribeUpdater->updateSubscriptionByResponse($subscription, $response);
            $this->subscriptionLimitCompleter->finishProcess($response, $subscription);

            if ($this->resultSuccessChecker->isSuccessful($response)) {
                $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
            }

            return [$subscription, $response];

        } catch (SubscribingProcessException $exception) {
            $subscription->setStatus(Subscription::IS_ERROR);
            $subscription->setError(sprintf('subscribing_process_exception:%s', $exception->getOperationPrefix()));
            throw $exception;
        } finally {
            $this->entitySaveHelper->persistAndSave($subscription);
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


    /**
     * @param Subscription     $existingSubscription
     * @param SubscriptionPack $plan
     * @param array            $additionalData
     *
     * @return ProcessResult
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws SubscribingProcessException
     */
    public function resubscribe(
        Subscription $existingSubscription,
        SubscriptionPack $plan,
        $additionalData = []
    ): ProcessResult
    {
        $subscription = $existingSubscription;

        try {


            $starter  = $this->subscribeProcessStarterProvider->get($plan->getCarrier());
            $response = $starter->startResubscribe($subscription, $plan, $additionalData);

            $this->onSubscribeUpdater->updateSubscriptionByResponse($subscription, $response);

            $user = $subscription->getUser();

            if ($this->resultSuccessChecker->isSuccessful($response)) {
                $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
            }


            $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
            return $response;

        } catch (SubscribingProcessException $exception) {
            $subscription->setStatus(Subscription::IS_ERROR);
            throw $exception;
        } finally {
            $this->entitySaveHelper->persistAndSave($subscription);
        }
    }

}