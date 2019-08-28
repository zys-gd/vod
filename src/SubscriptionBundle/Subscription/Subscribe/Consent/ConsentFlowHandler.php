<?php

namespace SubscriptionBundle\Subscription\Subscribe\Consent;

use Doctrine\ORM\NonUniqueResultException;
use ExtrasBundle\Controller\Traits\ResponseTrait;
use ExtrasBundle\Utils\UrlParamAppender;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\RouteProvider;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Common\AfterSubscriptionProcessTracker;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\Subscription\Subscribe\Common\{AffiliateNotifier,
    CommonResponseCreator,
    SubscriptionEligibilityChecker,
    SubscriptionEventTracker};
use SubscriptionBundle\Subscription\Subscribe\Handler\{HasCustomResponses};
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Subscription\Subscribe\Subscriber;
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
     * @var AffiliateNotifier
     */
    private $affiliateNotifier;

    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var \SubscriptionBundle\Subscription\Subscribe\Common\AfterSubscriptionProcessTracker
     */
    private $afterSubscriptionProcessTracker;

    /**
     * ConsentFlowHandler constructor
     *
     * @param LoggerInterface                                                                   $logger
     * @param SubscriptionExtractor                                                             $subscriptionExtractor
     * @param SubscriptionPackProvider                                                          $subscriptionPackProvider
     * @param Subscriber                                                                        $subscriber
     * @param EntitySaveHelper                                                                  $entitySaveHelper
     * @param RouteProvider                                                                     $routeProvider
     * @param SubscriptionEligibilityChecker                                                    $subscriptionEligibilityChecker
     * @param UrlParamAppender                                                                  $urlParamAppender
     * @param RouterInterface                                                                   $router
     * @param CommonResponseCreator                                                             $commonResponseCreator
     * @param AffiliateNotifier                                                                 $affiliateNotifier
     * @param CampaignExtractor                                                                 $campaignExtractor
     * @param \SubscriptionBundle\Subscription\Subscribe\Common\AfterSubscriptionProcessTracker $afterSubscriptionProcessTracker
     */
    public function __construct(
        LoggerInterface $logger,
        SubscriptionExtractor $subscriptionExtractor,
        SubscriptionPackProvider $subscriptionPackProvider,
        Subscriber $subscriber,
        EntitySaveHelper $entitySaveHelper,
        RouteProvider $routeProvider,
        SubscriptionEligibilityChecker $subscriptionEligibilityChecker,
        UrlParamAppender $urlParamAppender,
        RouterInterface $router,
        CommonResponseCreator $commonResponseCreator,
        AffiliateNotifier $affiliateNotifier,
        CampaignExtractor $campaignExtractor,
        AfterSubscriptionProcessTracker $afterSubscriptionProcessTracker


    )
    {
        $this->logger                          = $logger;
        $this->subscriptionExtractor           = $subscriptionExtractor;
        $this->subscriptionPackProvider        = $subscriptionPackProvider;
        $this->subscriber                      = $subscriber;
        $this->entitySaveHelper                = $entitySaveHelper;
        $this->routeProvider                   = $routeProvider;
        $this->subscriptionEligibilityChecker  = $subscriptionEligibilityChecker;
        $this->urlParamAppender                = $urlParamAppender;
        $this->router                          = $router;
        $this->commonResponseCreator           = $commonResponseCreator;
        $this->affiliateNotifier               = $affiliateNotifier;
        $this->campaignExtractor = $campaignExtractor;
        $this->afterSubscriptionProcessTracker = $afterSubscriptionProcessTracker;
    }

    /**
     * @param Request            $request
     * @param User               $user
     * @param HasConsentPageFlow $subscriber
     *
     * @return Response
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws NonUniqueResultException
     */
    public function process(Request $request, User $user, HasConsentPageFlow $subscriber): Response
    {
        $subscription = $this->subscriptionExtractor->getExistingSubscriptionForUser($user);

        if (empty($subscription)) {
            $this->logger->debug('Processing `consent subscribe` action', [
                'user'    => $user,
                'request' => $request
            ]);

            return $this->handleSubscribe($request, $user, $subscriber);
        }

        if ($this->subscriptionEligibilityChecker->isStatusOkForResubscribe($subscription)) {
            $this->logger->debug('Processing `consent subscribe` action', [
                'user'    => $user,
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
            $updatedUrl   = $this->urlParamAppender->appendUrl($redirect_url, [
                'err_handle' => 'already_subscribed'
            ]);

            return new RedirectResponse($updatedUrl);
        }
    }

    /**
     * @param Request            $request
     * @param User               $user
     * @param HasConsentPageFlow $subscriber
     *
     * @return Response
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws NonUniqueResultException
     */
    public function handleSubscribe(Request $request, User $user, HasConsentPageFlow $subscriber): Response
    {
        $additionalData   = $subscriber->getAdditionalSubscribeParams($request, $user);
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);
        $campaign         = $this->campaignExtractor->getCampaignFromSession($request->getSession());

        /** @var ProcessResult $result */
        list($newSubscription, $result) = $this->subscriber->subscribe($user, $subscriptionPack, $additionalData);

        $this->afterSubscriptionProcessTracker->trackSubscribe($result, $newSubscription, $subscriber, $campaign);

        $subscriber->afterProcess($newSubscription, $result);
        $this->entitySaveHelper->saveAll();

        return new RedirectResponse($this->routeProvider->getLinkToHomepage());
    }

    /**
     * @param Request            $request
     * @param User               $user
     * @param HasConsentPageFlow $subscriber
     * @param Subscription       $subscription
     *
     * @return Response
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
            $result         = $this->subscriber->resubscribe($subscription, $subscriptionPack, $additionalData);
        } else {
            $this->logger->debug('Resubscription is not allowed.', [
                'packId'      => $subscriptionPack->getUuid(),
                'carrierName' => $subscriptionPack->getName()
            ]);

            if ($request->get('is_ajax_request', null)) {
                return $this->getSimpleJsonResponse('', 200, [], ['resub_not_allowed' => true]);
            } else {
                return new RedirectResponse($this->router->generate('resub_not_allowed'));
            }
        }

        $this->afterSubscriptionProcessTracker->trackResubscribe($result, $subscription, $subscriber, null, true);

        $subscriber->afterProcess($subscription, $result);
        $this->entitySaveHelper->saveAll();

        return $this->commonResponseCreator->createCommonHttpResponse($request, $user);
    }
}