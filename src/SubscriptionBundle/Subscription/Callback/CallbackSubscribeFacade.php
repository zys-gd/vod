<?php


namespace SubscriptionBundle\Subscription\Callback;


use App\Domain\Entity\Carrier;
use IdentificationBundle\Entity\User;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Common\Handler\SubscriptionCallbackHandler;
use SubscriptionBundle\Subscription\Callback\Common\SubscriptionPreparer;
use SubscriptionBundle\Subscription\Subscribe\Common\SubscriptionEventTracker;

class CallbackSubscribeFacade
{
    /**
     * @var SubscriptionCallbackHandler
     */
    private $subscriptionCallbackHandler;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var SubscriptionPreparer
     */
    private $subscriptionPreparer;
    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;
    /**
     * @var UserInfoMapper
     */
    private $infoMapper;
    /**
     * @var AffiliateSender
     */
    private $affiliateSender;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SubscribeFacade constructor.
     *
     * @param SubscriptionCallbackHandler $subscriptionCallbackHandler
     * @param EntitySaveHelper            $entitySaveHelper
     * @param SubscriptionPreparer        $subscriptionPreparer
     * @param SubscriptionEventTracker    $subscriptionEventTracker
     * @param ApiConnector                $apiConnector
     * @param UserInfoMapper              $infoMapper
     * @param AffiliateSender             $affiliateService
     * @param LoggerInterface             $logger
     */
    public function __construct(
        SubscriptionCallbackHandler $subscriptionCallbackHandler,
        EntitySaveHelper $entitySaveHelper,
        SubscriptionPreparer $subscriptionPreparer,
        SubscriptionEventTracker $subscriptionEventTracker,
        ApiConnector $apiConnector,
        UserInfoMapper $infoMapper,
        AffiliateSender $affiliateService,
        LoggerInterface $logger
    )
    {
        $this->subscriptionCallbackHandler = $subscriptionCallbackHandler;
        $this->entitySaveHelper            = $entitySaveHelper;
        $this->subscriptionPreparer        = $subscriptionPreparer;
        $this->subscriptionEventTracker    = $subscriptionEventTracker;
        $this->crossSubscriptionApi        = $apiConnector;
        $this->infoMapper                  = $infoMapper;
        $this->affiliateSender             = $affiliateService;
        $this->logger                      = $logger;
    }

    /**
     * @param ProcessResult $processResponse
     * @param array|null    $affiliateToken
     *
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     */
    public function doFullCallbackSubscribe(ProcessResult $processResponse, array $affiliateToken = null)
    {
        $this->logger->debug('doFullCallbackSubscribe start subscription at:', [time()]);
        /** @var Carrier $carrier */
        /** @var User $user */
        /** @var Subscription $subscription */
        [$carrier, $user, $subscription] = $this->subscriptionPreparer->makeUserWithSubscription($processResponse);
        $subscription->setAffiliateToken(json_encode($affiliateToken));
        $this->subscriptionCallbackHandler->doProcess($subscription, $processResponse);
        $this->entitySaveHelper->persistAndSave($subscription);
        $this->logger->debug('doFullCallbackSubscribe creat subscription at:', [time()]);
        $this->logger->debug('doFullCallbackSubscribe receive token', [$affiliateToken]);
        $this->subscriptionEventTracker->trackSubscribe($subscription, $processResponse);

        $userInfo = $this->infoMapper->mapFromUser($user);
        $this->affiliateSender->checkAffiliateEligibilityAndSendEvent(
            $subscription,
            $userInfo,
            $affiliateToken['cid'] ?? null,
            $affiliateToken ?? []
        );

        $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
    }
}