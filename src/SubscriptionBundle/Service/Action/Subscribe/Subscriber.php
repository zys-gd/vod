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
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\Action\Common\FakeResponseProvider;
use SubscriptionBundle\Service\Action\Common\PromotionalResponseChecker;
use SubscriptionBundle\Service\Action\Subscribe\Common\SubscribePerformer;
use SubscriptionBundle\Service\Action\Subscribe\Common\SubscribePromotionalPerformer;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\CapConstraint\SubscriptionCounterUpdater;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\Notification\Notifier;
use SubscriptionBundle\Service\SubscriptionCreator;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimitCompleter;
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
     * @var SubscribePerformer
     */
    private $subscribePerformer;
    /**
     * @var SubscribePromotionalPerformer
     */
    private $subscribePromotionalPerformer;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;


    /**
     * Subscriber constructor.
     *
     * @param LoggerInterface               $logger
     * @param EntitySaveHelper              $entitySaveHelper
     * @param SessionInterface              $session
     * @param SubscriptionCreator           $subscriptionCreator
     * @param PromotionalResponseChecker    $promotionalResponseChecker
     * @param FakeResponseProvider          $fakeResponseProvider
     * @param Notifier                      $notifier
     * @param SubscribeProcess              $subscribeProcess
     * @param OnSubscribeUpdater            $onSubscribeUpdater
     * @param SubscribeParametersProvider   $subscribeParametersProvider
     * @param SubscriptionLimitCompleter    $subscriptionLimitCompleter
     * @param SubscriptionSerializer        $subscriptionSerializer
     * @param SubscribePerformer            $subscribePerformer
     * @param SubscribePromotionalPerformer $subscribePromotionalPerformer
     * @param CampaignExtractor             $campaignExtractor
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
        SubscriptionSerializer $subscriptionSerializer,
        SubscribePerformer $subscribePerformer,
        SubscribePromotionalPerformer $subscribePromotionalPerformer,
        CampaignExtractor $campaignExtractor
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
        $this->campaignExtractor             = $campaignExtractor;
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

        $campaign = $this->campaignExtractor->getCampaignForSubscription($subscription);
        $isFreeTrialSubscriptionFromCampaign = $campaign && $campaign->isFreeTrialSubscription();

        try{
            if (
                ($subscription->getSubscriptionPack()->isFirstSubscriptionPeriodIsFree() || $isFreeTrialSubscriptionFromCampaign)
                && !$subscription->getSubscriptionPack()->isProviderManagedSubscriptions()
            ) {
                $tierIdWithZeroValue = $this->getPriceTierIdWithZeroValue($subscription->getSubscriptionPack()->getCarrier());
                $subscription->setPromotionTierId($tierIdWithZeroValue);
            }
        } catch (\Throwable $e) { }

        try {

            if ($this->promotionalResponseChecker->isPromotionalResponseNeeded($subscription)) {
                $response = $this->subscribePromotionalPerformer->doSubscribe($subscription);
                if (!$plan->isFirstSubscriptionPeriodIsFree() && !$isFreeTrialSubscriptionFromCampaign) {
                    $this->subscribePerformer->doSubscribe($subscription, $additionalData);
                }
            }
            else {
                $response = $this->subscribePerformer->doSubscribe($subscription, $additionalData);

            }

            $this->onSubscribeUpdater->updateSubscriptionByResponse($subscription, $response);
            $this->subscriptionLimitCompleter->finishProcess($response, $subscription);

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
            }
            else {
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
        $campaign = $this->campaignExtractor->getCampaignForSubscription($subscription);
        $isFreeTrialSubscriptionFromCampaign = $campaign && $campaign->isFreeTrialSubscription();

        $subscriptionPack = $subscription->getSubscriptionPack();
        if ($subscriptionPack->isFirstSubscriptionPeriodIsFree() || $isFreeTrialSubscriptionFromCampaign
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