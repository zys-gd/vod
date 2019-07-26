<?php

namespace SubscriptionBundle\Subscription\Subscribe\Consent;

use Doctrine\ORM\NonUniqueResultException;
use ExtrasBundle\Controller\Traits\ResponseTrait;
use ExtrasBundle\Utils\UrlParamAppender;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\RouteProvider;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Subscription\Subscribe\Common\{CommonResponseCreator,
    SubscriptionEligibilityChecker,
    SubscriptionEventTracker};
use SubscriptionBundle\Subscription\Subscribe\Handler\{HasCustomAffiliateTrackingRules,
    HasCustomPiwikTrackingRules,
    HasCustomResponses};
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Subscription\Subscribe\Subscriber;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ConsentFlowHandler
 */
class ConsentFlowHandler
{
    use ResponseTrait;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;

    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;

    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;

    /**
     * @var RouteProvider
     */
    private $routeProvider;

    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    /**
     * @var SubscriptionEligibilityChecker
     */
    private $subscriptionEligibilityChecker;

    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CommonResponseCreator
     */
    private $commonResponseCreator;

    /**
     * ConsentFlowHandler constructor
     *
     * @param LoggerInterface $logger
     * @param SubscriptionExtractor $subscriptionExtractor
     * @param SubscriptionPackProvider $subscriptionPackProvider
     * @param Subscriber $subscriber
     * @param SubscriptionEventTracker $subscriptionEventTracker
     * @param EntitySaveHelper $entitySaveHelper
     * @param RouteProvider $routeProvider
     * @param SubscriptionEligibilityChecker $subscriptionEligibilityChecker
     * @param UrlParamAppender $urlParamAppender
     * @param RouterInterface $router
     * @param CommonResponseCreator $commonResponseCreator
     */
    public function __construct(
        LoggerInterface $logger,
        SubscriptionExtractor $subscriptionExtractor,
        SubscriptionPackProvider $subscriptionPackProvider,
        Subscriber $subscriber,
        SubscriptionEventTracker $subscriptionEventTracker,
        EntitySaveHelper $entitySaveHelper,
        RouteProvider $routeProvider,
        SubscriptionEligibilityChecker $subscriptionEligibilityChecker,
        UrlParamAppender $urlParamAppender,
        RouterInterface $router,
        CommonResponseCreator $commonResponseCreator
    ) {
        $this->logger = $logger;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->subscriber = $subscriber;
        $this->subscriptionEventTracker = $subscriptionEventTracker;
        $this->entitySaveHelper = $entitySaveHelper;
        $this->routeProvider = $routeProvider;
        $this->subscriptionEligibilityChecker = $subscriptionEligibilityChecker;
        $this->urlParamAppender = $urlParamAppender;
        $this->router = $router;
        $this->commonResponseCreator = $commonResponseCreator;
    }

    /**
     * @param Request $request
     * @param User $user
     * @param HasConsentPageFlow $subscriber
     *
     * @return Response
     *
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws NonUniqueResultException
     */
    public function process(Request $request, User $user, HasConsentPageFlow $subscriber): Response
    {
        $subscription = $this->subscriptionExtractor->getExistingSubscriptionForUser($user);

        if (empty($subscription)) {
            $this->logger->debug('Processing `consent subscribe` action', [
                'user' => $user,
                'request' => $request
            ]);

            return $this->handleSubscribe($request, $user, $subscriber);
        }

        if ($this->subscriptionEligibilityChecker->isStatusOkForResubscribe($subscription)) {
            $this->logger->debug('Processing `consent subscribe` action', [
                'user' => $user,
                'request' => $request
            ]);

            return $this->handleResubscribe($request, $user, $subscriber, $subscription);
        } else {
            $this->logger->debug('`Subscribe` is not possible. User already have an active subscription.');

            if (
                $subscriber instanceof HasCustomResponses &&
                $response = $subscriber->createResponseForExistingSubscription($request, $user, $subscription)
            ) {
                return $response;
            }

            $redirect_url = $this->router->generate('index');
            $updatedUrl = $this->urlParamAppender->appendUrl($redirect_url, [
                'err_handle' => 'already_subscribed'
            ]);

            return new RedirectResponse($updatedUrl);
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @param HasConsentPageFlow $subscriber
     *
     * @return Response
     *
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws NonUniqueResultException
     */
    public function handleSubscribe(Request $request, User $user, HasConsentPageFlow $subscriber): Response
    {
        $additionalData = $subscriber->getAdditionalSubscribeParams($request, $user);

        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        /** @var ProcessResult $result */
        list($newSubscription, $result) = $this->subscriber->subscribe($user, $subscriptionPack, $additionalData);

        if ($subscriber instanceof HasCustomAffiliateTrackingRules) {
            $isAffTracked = $subscriber->isAffiliateTrackedForSub($result);
        } else {
            $isAffTracked = ($result->isSuccessful() && $result->isFinal());
        }

        if ($isAffTracked) {
            $this->subscriptionEventTracker->trackAffiliate($newSubscription);
        }

        if ($subscriber instanceof HasCustomPiwikTrackingRules) {
            $isPiwikTracked = $subscriber->isPiwikTrackedForSub($result);
        } else {
            $isPiwikTracked = ($result->isFailedOrSuccessful() && $result->isFinal());
        }

        if ($isPiwikTracked) {
            $this->subscriptionEventTracker->trackPiwikForSubscribe($newSubscription, $result);
        }

        $subscriber->afterProcess($newSubscription, $result);
        $this->entitySaveHelper->saveAll();

        return new RedirectResponse($this->routeProvider->getLinkToHomepage());
    }

    /**
     * @param Request $request
     * @param User $user
     * @param HasConsentPageFlow $subscriber
     * @param Subscription $subscription
     *
     * @return Response
     *
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws NonUniqueResultException
     */
    public function handleResubscribe(
        Request $request,
        User $user,
        HasConsentPageFlow $subscriber,
        Subscription $subscription
    ): Response
    {
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        if ($this->subscriptionEligibilityChecker->isResubscriptionAfterUnsubscribeCase($subscription, $subscriptionPack)
            || $this->subscriptionEligibilityChecker->isNotFullyPaidSubscriptionCase($subscription)
        ) {
            $this->logger->debug('Resubscription is allowed. Doing resubscribe', [
                'packId'      => $subscriptionPack->getUuid(),
                'carrierName' => $subscriptionPack->getName()
            ]);

            $additionalData = $subscriber->getAdditionalSubscribeParams($request, $user);
            $result = $this->subscriber->resubscribe($subscription, $subscriptionPack, $additionalData);
        } else {
            $this->logger->debug('Resubscription is not allowed.', [
                'packId' => $subscriptionPack->getUuid(),
                'carrierName' => $subscriptionPack->getName()
            ]);

            if ($request->get('is_ajax_request', null)) {
                return $this->getSimpleJsonResponse('', 200, [], ['resub_not_allowed' => true]);
            } else {
                return new RedirectResponse($this->router->generate('resub_not_allowed'));
            }
        }

        if ($subscriber instanceof HasCustomAffiliateTrackingRules) {
            $isAffTracked = $subscriber->isAffiliateTrackedForResub($result);
        } else {
            $isAffTracked = ($result->isSuccessful() && $result->isFinal());
            $this->logger->debug('Is need to track affiliate log?', [
                'result'       => $result,
                'isAffTracked' => $isAffTracked
            ]);
        }

        if ($isAffTracked) {
            $this->subscriptionEventTracker->trackAffiliate($subscription);
        }

        if ($subscriber instanceof HasCustomPiwikTrackingRules) {
            $isPiwikTracked = $subscriber->isPiwikTrackedForResub($result);
        } else {
            $isPiwikTracked = ($result->isFailedOrSuccessful() && $result->isFinal());;
        }

        if ($isPiwikTracked) {
            $this->subscriptionEventTracker->trackPiwikForResubscribe($subscription, $result);
        }

        $subscriber->afterProcess($subscription, $result);
        $this->entitySaveHelper->saveAll();

        return $this->commonResponseCreator->createCommonHttpResponse($request, $user);
    }
}