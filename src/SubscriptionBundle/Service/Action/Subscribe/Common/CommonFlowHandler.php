<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 14:06
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Common;

use App\Domain\Entity\Campaign;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use ExtrasBundle\Utils\UrlParamAppender;
use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Piwik\SubscriptionStatisticSender;
use SubscriptionBundle\Service\Action\Common\RedirectUrlNullifier;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomPiwikTrackingRules;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Service\Action\Subscribe\Subscriber;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use SubscriptionBundle\Service\ZeroCreditSubscriptionChecking;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CommonFlowHandler
{

    use ResponseTrait;
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
     * @var SubscriptionExtractor
     */
    private $subscriptionProvider;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RedirectUrlNullifier
     */
    private $redirectUrlNullifier;
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
     * @var RouterInterface
     */
    private $router;
    /**
     * @var AffiliateSender
     */
    private $affiliateService;
    /**
     * @var SubscriptionStatisticSender
     */
    private $subscriptionStatisticSender;
    /**
     * @var UserInfoMapper
     */
    private $infoMapper;
    /**
     * @var EntityManagerHelper
     */
    private $entitySaveHelper;
    /**
     * @var string
     */
    private $resubNotAllowedRoute;
    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;

    /**
     * CommonSubscriber constructor.
     *
     * @param SubscriptionExtractor          $subscriptionProvider
     * @param SubscriptionPackProvider       $subscriptionPackProvider
     * @param Subscriber                     $subscriber
     * @param SubscriptionEligibilityChecker $checker
     * @param LoggerInterface                $logger
     * @param RedirectUrlNullifier           $redirectUrlNullifier
     * @param SubscriptionHandlerProvider    $handlerProvider
     * @param CommonResponseCreator          $commonResponseCreator
     * @param UrlParamAppender               $urlParamAppender
     * @param RouterInterface                $router
     * @param AffiliateSender                $affiliateService
     * @param SubscriptionStatisticSender    $subscriptionStatisticSender
     * @param UserInfoMapper                 $infoMapper
     * @param EntitySaveHelper               $entitySaveHelper
     * @param string                         $resubNotAllowedRoute
     * @param SubscriptionEventTracker       $subscriptionEventTracker
     */
    public function __construct(
        SubscriptionExtractor $subscriptionProvider,
        SubscriptionPackProvider $subscriptionPackProvider,
        Subscriber $subscriber,
        SubscriptionEligibilityChecker $checker,
        LoggerInterface $logger,
        RedirectUrlNullifier $redirectUrlNullifier,
        SubscriptionHandlerProvider $handlerProvider,
        CommonResponseCreator $commonResponseCreator,
        UrlParamAppender $urlParamAppender,
        RouterInterface $router,
        AffiliateSender $affiliateService,
        SubscriptionStatisticSender $subscriptionStatisticSender,
        UserInfoMapper $infoMapper,
        EntitySaveHelper $entitySaveHelper,
        string $resubNotAllowedRoute,
        SubscriptionEventTracker $subscriptionEventTracker
    )
    {
        $this->subscriptionPackProvider       = $subscriptionPackProvider;
        $this->subscriber                     = $subscriber;
        $this->checker                        = $checker;
        $this->subscriptionProvider           = $subscriptionProvider;
        $this->logger                         = $logger;
        $this->redirectUrlNullifier           = $redirectUrlNullifier;
        $this->handlerProvider                = $handlerProvider;
        $this->commonResponseCreator          = $commonResponseCreator;
        $this->urlParamAppender               = $urlParamAppender;
        $this->router                         = $router;
        $this->affiliateService               = $affiliateService;
        $this->subscriptionStatisticSender    = $subscriptionStatisticSender;
        $this->infoMapper                     = $infoMapper;
        $this->entitySaveHelper               = $entitySaveHelper;
        $this->resubNotAllowedRoute           = $resubNotAllowedRoute;
        $this->subscriptionEventTracker       = $subscriptionEventTracker;
    }


    /**
     * @param Request $request
     * @param User    $User
     *
     * @return Response
     * @throws ActiveSubscriptionPackNotFound
     * @throws ExistingSubscriptionException
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

        if ($this->checker->isStatusOkForTryAgainSubscription($subscription)) {
            return $this->handleSubscribe($request, $User, $subscriber);
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
                return new RedirectResponse($this->router->generate($this->resubNotAllowedRoute));
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

    /**
     * @param Request       $request
     * @param User          $user
     * @param HasCommonFlow $subscriber
     *
     * @return null|Response
     * @throws ActiveSubscriptionPackNotFound
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function handleSubscribe(Request $request, User $user, HasCommonFlow $subscriber): Response
    {
        $additionalData   = $subscriber->getAdditionalSubscribeParams($request, $user);
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        /** @var ProcessResult $result */
        list($newSubscription, $result) = $this->subscriber->subscribe($user, $subscriptionPack, $additionalData);

        if ($subscriber instanceof HasCustomAffiliateTrackingRules) {
            $isAffTracked = $subscriber->isAffiliateTrackedForSub($result, $user);
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
            $customResponse = $subscriber->createResponseForSuccessfulSubscribe($request, $user, $newSubscription)) {
            return $customResponse;
        }

        return $this->commonResponseCreator->createCommonHttpResponse($request, $user);

    }

}