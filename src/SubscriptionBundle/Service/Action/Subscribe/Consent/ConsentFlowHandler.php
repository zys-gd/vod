<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Consent;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\RouteProvider;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Service\Action\Subscribe\Common\SubscriptionEventTracker;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomPiwikTrackingRules;
use SubscriptionBundle\Service\Action\Subscribe\Subscriber;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ConsentFlowHandler
 */
class ConsentFlowHandler
{
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
     * @var EntityManagerHelper
     */
    private $entityManagerHelper;

    /**
     * ConsentFlowHandler constructor
     *
     * @param LoggerInterface $logger
     * @param SubscriptionExtractor $subscriptionExtractor
     * @param SubscriptionPackProvider $subscriptionPackProvider
     * @param Subscriber $subscriber
     * @param SubscriptionEventTracker $subscriptionEventTracker
     * @param EntitySaveHelper $entityManagerHelper
     * @param RouteProvider $routeProvider
     */
    public function __construct(
        LoggerInterface $logger,
        SubscriptionExtractor $subscriptionExtractor,
        SubscriptionPackProvider $subscriptionPackProvider,
        Subscriber $subscriber,
        SubscriptionEventTracker $subscriptionEventTracker,
        EntitySaveHelper $entityManagerHelper,
        RouteProvider $routeProvider
    ) {
        $this->logger = $logger;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->subscriber = $subscriber;
        $this->subscriptionEventTracker = $subscriptionEventTracker;
        $this->entityManagerHelper = $entityManagerHelper;
        $this->routeProvider = $routeProvider;
    }

    /**
     * @param Request $request
     * @param User $user
     * @param HasConsentPageFlow $subscriber
     *
     * @return Response
     *
     * @throws ActiveSubscriptionPackNotFound
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
        } else {
            $this->logger->debug('Processing `consent resubscribe` action', [
                'user' => $user,
                'request' => $request
            ]);

            return new RedirectResponse($this->routeProvider->getLinkToHomepage());
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @param HasConsentPageFlow $subscriber
     *
     * @return Response
     *
     * @throws ActiveSubscriptionPackNotFound
     */
    public function handleSubscribe(Request $request, User $user, HasConsentPageFlow $subscriber): Response
    {
        $additionalData = $subscriber->getAdditionalSubscribeParams($request, $user);

        if (empty($additionalData['subscription_contract_id'])) {
            throw new BadRequestHttpException("Can't process subscribe, required parameter `subscription_contract_id` not found");
        }

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
        $this->entityManagerHelper->saveAll();

        return new RedirectResponse($this->routeProvider->getLinkToHomepage());
    }
}