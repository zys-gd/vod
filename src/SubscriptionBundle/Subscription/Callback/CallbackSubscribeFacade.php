<?php


namespace SubscriptionBundle\Subscription\Callback;


use App\Domain\Entity\Carrier;
use IdentificationBundle\Entity\User;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
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
     * SubscribeFacade constructor.
     *
     * @param SubscriptionCallbackHandler $subscriptionCallbackHandler
     * @param EntitySaveHelper            $entitySaveHelper
     * @param SubscriptionPreparer        $subscriptionPreparer
     * @param SubscriptionEventTracker    $subscriptionEventTracker
     * @param ApiConnector                $apiConnector
     * @param UserInfoMapper              $infoMapper
     * @param AffiliateSender             $affiliateService
     */
    public function __construct(
        SubscriptionCallbackHandler $subscriptionCallbackHandler,
        EntitySaveHelper $entitySaveHelper,
        SubscriptionPreparer $subscriptionPreparer,
        SubscriptionEventTracker $subscriptionEventTracker,
        ApiConnector $apiConnector,
        UserInfoMapper $infoMapper,
        AffiliateSender $affiliateService
    )
    {
        $this->subscriptionCallbackHandler = $subscriptionCallbackHandler;
        $this->entitySaveHelper            = $entitySaveHelper;
        $this->subscriptionPreparer        = $subscriptionPreparer;
        $this->subscriptionEventTracker    = $subscriptionEventTracker;
        $this->crossSubscriptionApi        = $apiConnector;
        $this->infoMapper                  = $infoMapper;
        $this->affiliateSender             = $affiliateService;
    }

    /**
     * @param ProcessResult $processResponse
     * @param array|null    $affiliateToken
     *
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     */
    public function doFullCallbackSubscribe(ProcessResult $processResponse, array $affiliateToken = null)
    {
        /** @var Carrier $carrier */
        /** @var User $user */
        /** @var Subscription $subscription */
        [$carrier, $user, $subscription] = $this->subscriptionPreparer->makeUserWithSubscription($processResponse);
        $subscription->setAffiliateToken(json_encode($affiliateToken));
        $this->subscriptionCallbackHandler->doProcess($subscription, $processResponse);
        $this->entitySaveHelper->persistAndSave($subscription);
        $this->subscriptionEventTracker->trackSubscribe($subscription, $processResponse);

        $userInfo = $this->infoMapper->mapFromUser($user);
        $this->affiliateSender->checkAffiliateEligibilityAndSendEvent(
            $subscription,
            $userInfo,
            $affiliateToken['cid'] ?? null,
            $affiliateToken
        );

        $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
    }
}