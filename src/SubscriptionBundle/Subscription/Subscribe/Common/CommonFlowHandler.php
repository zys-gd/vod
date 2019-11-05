<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 14:06
 */

namespace SubscriptionBundle\Subscription\Subscribe\Common;

use ExtrasBundle\Controller\Traits\ResponseTrait;
use ExtrasBundle\Utils\UrlParamAppender;
use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\RouteProvider;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\Subscription\Subscribe\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Subscription\Subscribe\Common\AfterSubscriptionProcessTracker;
use SubscriptionBundle\Subscription\Subscribe\Common\PendingSubscriptionCreator;
use SubscriptionBundle\Subscription\Subscribe\Subscriber;
use SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommonFlowHandler
{

    use ResponseTrait;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionProvider;
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var Subscriber
     */
    private $subscriber;
    /**
     * @var SubscriptionEligibilityChecker
     */
    private $checker;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubscriptionHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var CommonResponseCreator
     */
    private $commonResponseCreator;
    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;

    private $zeroCreditSubscriptionChecking;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var AffiliateNotifier
     */
    private $affiliateNotifier;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var AfterSubscriptionProcessTracker
     */
    private $afterSubscriptionProcessTracker;
    /**
     * @var PendingSubscriptionCreator
     */
    private $pendingSubscriptionCreator;


    /**
     * CommonSubscriber constructor.
     *
     * @param SubscriptionExtractor           $subscriptionProvider
     * @param SubscriptionPackProvider        $subscriptionPackProvider
     * @param Subscriber                      $subscriber
     * @param SubscriptionEligibilityChecker  $checker
     * @param LoggerInterface                 $logger
     * @param SubscriptionHandlerProvider     $handlerProvider
     * @param CommonResponseCreator           $commonResponseCreator
     * @param UrlParamAppender                $urlParamAppender
     * @param EntitySaveHelper                $entitySaveHelper
     * @param SubscriptionEventTracker        $subscriptionEventTracker
     * @param ZeroCreditSubscriptionChecking  $zeroCreditSubscriptionChecking
     * @param RouteProvider                   $routeProvider
     * @param AffiliateNotifier               $affiliateNotifier
     * @param CampaignExtractor               $campaignExtractor
     * @param AfterSubscriptionProcessTracker $afterSubscriptionProcessTracker
     * @param PendingSubscriptionCreator      $pendingSubscriptionCreator
     */
    public function __construct(
        SubscriptionExtractor $subscriptionProvider,
        SubscriptionPackProvider $subscriptionPackProvider,
        Subscriber $subscriber,
        SubscriptionEligibilityChecker $checker,
        LoggerInterface $logger,
        SubscriptionHandlerProvider $handlerProvider,
        CommonResponseCreator $commonResponseCreator,
        UrlParamAppender $urlParamAppender,
        EntitySaveHelper $entitySaveHelper,
        SubscriptionEventTracker $subscriptionEventTracker,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking,
        RouteProvider $routeProvider,
        AffiliateNotifier $affiliateNotifier,
        CampaignExtractor $campaignExtractor,
        AfterSubscriptionProcessTracker $afterSubscriptionProcessTracker,
        PendingSubscriptionCreator $pendingSubscriptionCreator
    )
    {
        $this->subscriptionProvider            = $subscriptionProvider;
        $this->subscriptionPackProvider        = $subscriptionPackProvider;
        $this->subscriber                      = $subscriber;
        $this->checker                         = $checker;
        $this->logger                          = $logger;
        $this->handlerProvider                 = $handlerProvider;
        $this->commonResponseCreator           = $commonResponseCreator;
        $this->urlParamAppender                = $urlParamAppender;
        $this->entitySaveHelper                = $entitySaveHelper;
        $this->subscriptionEventTracker        = $subscriptionEventTracker;
        $this->routeProvider                   = $routeProvider;
        $this->affiliateNotifier               = $affiliateNotifier;
        $this->zeroCreditSubscriptionChecking  = $zeroCreditSubscriptionChecking;
        $this->campaignExtractor               = $campaignExtractor;
        $this->afterSubscriptionProcessTracker = $afterSubscriptionProcessTracker;
        $this->pendingSubscriptionCreator      = $pendingSubscriptionCreator;
    }

    /**
     * @param Request $request
     * @param User    $User
     *
     * @return Response
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws \SubscriptionBundle\Subscription\Subscribe\Exception\ExistingSubscriptionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function process(Request $request, User $User): Response
    {
        $UserId         = $User->getUuid();
        $UserIdentifier = $User->getIdentifier();
        $this->logger->debug('Processing `subscribe` action', [
            'UserId'  => $UserId,
            'msidsn'  => $UserIdentifier,
            'request' => $request
        ]);

        /** @var HasCommonFlow $subscriber */
        $subscriber = $this->handlerProvider->getSubscriber($User->getCarrier());
        if (
            $subscriber instanceof HasCustomResponses &&
            $response = $subscriber->createResponseBeforeSubscribeAttempt($request, $User)
        ) {
            return $response;
        }

        $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($User);

        if (empty($subscription)) {
            $newSubscription = $this->createNewSubscription($request, $User);
            return $this->handleSubscribeAttempt($request, $User, $newSubscription, $subscriber);
        }

        if ($this->checker->isStatusOkForTryAgainSubscription($subscription)) {
            return $this->handleSubscribeAttempt($request, $User, $subscription, $subscriber);
        }

        if ($this->checker->isStatusOkForResubscribe($subscription)) {
            return $this->handleResubscribeAttempt($request, $User, $subscription, $subscriber);

        }

        $this->logger->debug('`Subscribe` is not possible. User already have an active subscription.');
        if (
            $subscriber instanceof HasCustomResponses &&
            $response = $subscriber->createResponseForExistingSubscription($request, $User, $subscription)
        ) {
            return $response;
        }


        $redirect     = $request->get('redirect', false);
        $redirect_url = $request->get('location', '/');
        $updatedUrl   = $this->urlParamAppender->appendUrl($redirect_url, [
            'err_handle' => 'already_subscribed'
        ]);

        if ($redirect) {
            return new RedirectResponse($updatedUrl);
        }

        throw new ExistingSubscriptionException('You already have an active subscription.', $subscription);

    }

    /**
     * @param Request       $request
     * @param User          $User
     * @param Subscription  $subscription
     * @param HasCommonFlow $subscriber
     *
     * @return null|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function handleSubscribeAttempt(
        Request $request,
        User $User,
        Subscription $subscription,
        HasCommonFlow $subscriber
    ): Response
    {

        $additionalData = $subscriber->getAdditionalSubscribeParams($request, $User);
        $campaign       = $this->campaignExtractor->getCampaignFromSession($request->getSession());
        $result         = $this->subscriber->subscribe($subscription, $additionalData);

        $this->afterSubscriptionProcessTracker->track($result, $subscription, $subscriber, $campaign);

        $subscriber->afterProcess($subscription, $result);
        $this->entitySaveHelper->saveAll();

        if ($subscriber instanceof HasCustomResponses &&
            $customResponse = $subscriber->createResponseForSuccessfulSubscribe($request, $User, $subscription)) {
            return $customResponse;
        }

        return $this->commonResponseCreator->createCommonHttpResponse($request, $User);

    }

    /**
     * @param Request      $request
     * @param User         $user
     * @param Subscription $subscription
     * @param              $subscriber
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|RedirectResponse|Response
     * @throws ActiveSubscriptionPackNotFound
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function handleResubscribeAttempt(
        Request $request,
        User $user,
        Subscription $subscription,
        HasCommonFlow $subscriber
    ): Response
    {

        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);
        $subpackId        = $subscriptionPack->getUuid();
        $subpackName      = $subscriptionPack->getName();

        // We have same property at Carrier.
        // Maybe we need to remove this duplicate?
        if (
            $this->checker->isResubscriptionAfterUnsubscribeCase($subscription, $subscriptionPack) ||
            $this->checker->isNotFullyPaidSubscriptionCase($subscription)
        ) {
            $this->logger->debug('Resubscription is allowed. Doing resubscribe', [
                'packId'      => $subpackId,
                'carrierName' => $subpackName
            ]);

            $additionalData = $subscriber->getAdditionalSubscribeParams($request, $user);
            $result         = $this->subscriber->resubscribe($subscription, $subscriptionPack, $additionalData);

        } else {
            $this->logger->debug('Resubscription is not allowed.', [
                'packId'      => $subpackId,
                'carrierName' => $subpackName
            ]);

            if ($request->get('is_ajax_request', null)) {
                return $this->getSimpleJsonResponse('', 200, [], ['resub_not_allowed' => true]);
            } else {
                return new RedirectResponse($this->routeProvider->getResubNotAllowedRoute());
            }
        }

        $this->afterSubscriptionProcessTracker->track($result, $subscription, $subscriber, null, true);

        $subscriber->afterProcess($subscription, $result);
        $this->entitySaveHelper->saveAll();

        return $this->commonResponseCreator->createCommonHttpResponse($request, $user);
    }

    /**
     * @param Request $request
     * @param User    $User
     * @return Subscription
     * @throws ActiveSubscriptionPackNotFound
     */
    private function createNewSubscription(Request $request, User $User): Subscription
    {
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($User);
        $campaignData     = AffiliateVisitSaver::extractPageVisitData($request->getSession(), true);
        $newSubscription  = $this->pendingSubscriptionCreator->createPendingSubscription($User, $subscriptionPack, $campaignData);
        return $newSubscription;
    }

}