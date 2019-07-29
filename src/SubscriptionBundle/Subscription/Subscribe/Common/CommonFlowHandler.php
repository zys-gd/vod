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
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\RouteProvider;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\Subscription\Subscribe\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomPiwikTrackingRules;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
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
     * @var \SubscriptionBundle\Subscription\Common\SubscriptionExtractor
     */
    private $subscriptionProvider;
    /**
     * @var \SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider
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
    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;
    /**
     * @var RouteProvider
     */
    private $routeProvider;


    /**
     * CommonSubscriber constructor.
     *
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionExtractor                    $subscriptionProvider
     * @param \SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider                    $subscriptionPackProvider
     * @param Subscriber                                                                       $subscriber
     * @param SubscriptionEligibilityChecker                                                   $checker
     * @param LoggerInterface                                                                  $logger
     * @param SubscriptionHandlerProvider                                                      $handlerProvider
     * @param CommonResponseCreator                                                            $commonResponseCreator
     * @param UrlParamAppender                                                                 $urlParamAppender
     * @param EntitySaveHelper                                                                 $entitySaveHelper
     * @param SubscriptionEventTracker                                                         $subscriptionEventTracker
     * @param \SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     * @param \SubscriptionBundle\Subscription\Common\RouteProvider                            $routeProvider
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
        RouteProvider $routeProvider
    )
    {

        $this->subscriptionProvider           = $subscriptionProvider;
        $this->subscriptionPackProvider       = $subscriptionPackProvider;
        $this->subscriber                     = $subscriber;
        $this->checker                        = $checker;
        $this->logger                         = $logger;
        $this->handlerProvider                = $handlerProvider;
        $this->commonResponseCreator          = $commonResponseCreator;
        $this->urlParamAppender               = $urlParamAppender;
        $this->entitySaveHelper               = $entitySaveHelper;
        $this->subscriptionEventTracker       = $subscriptionEventTracker;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
        $this->routeProvider                  = $routeProvider;
    }


    /**
     * @param Request $request
     * @param User    $User
     *
     * @return Response
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws \SubscriptionBundle\Subscription\Subscribe\Exception\ExistingSubscriptionException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
        $subscriber   = $this->handlerProvider->getSubscriber($User->getCarrier());
        $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($User);

        if (empty($subscription)) {
            return $this->handleSubscribe($request, $User, $subscriber);
        }

        if ($this->checker->isStatusOkForResubscribe($subscription)) {
            return $this->handleResubscribeAttempt($request, $User, $subscription, $subscriber);

        } else {
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
    }

    /**
     * @param Request       $request
     * @param User          $User
     * @param HasCommonFlow $subscriber
     *
     * @return null|Response
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function handleSubscribe(Request $request, User $User, HasCommonFlow $subscriber): Response
    {

        $additionalData   = $subscriber->getAdditionalSubscribeParams($request, $User);
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($User);

        if ($this->zeroCreditSubscriptionChecking->isAvailable($request->getSession(), $subscriptionPack)) {
            $additionalData['zero_credit_sub_available'] = true;
        }

        /** @var ProcessResult $result */
        list($newSubscription, $result) = $this->subscriber->subscribe($User, $subscriptionPack, $additionalData);

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

        if ($subscriber instanceof HasCustomResponses &&
            $customResponse = $subscriber->createResponseForSuccessfulSubscribe($request, $User, $newSubscription)) {
            return $customResponse;
        }

        return $this->commonResponseCreator->createCommonHttpResponse($request, $User);

    }

    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     * @param              $subscriber
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|RedirectResponse|Response
     * @throws ActiveSubscriptionPackNotFound
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function handleResubscribeAttempt(
        Request $request,
        User $User,
        Subscription $subscription,
        HasCommonFlow $subscriber
    ): Response
    {

        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($User);
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

            $additionalData = $subscriber->getAdditionalSubscribeParams($request, $User);
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

        return $this->commonResponseCreator->createCommonHttpResponse($request, $User);
    }

}