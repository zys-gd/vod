<?php


namespace SubscriptionBundle\Subscription\SubscribeBack\Common;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Utils\UrlParamAppender;
use IdentificationBundle\Identification\Service\DeviceDataProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\User\Service\UserFactory;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Blacklist\BlacklistAttemptRegistrator;
use IdentificationBundle\Identification\Service\RouteProvider;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\Subscription\Subscribe\Common\AfterSubscriptionProcessTracker;
use SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter;
use SubscriptionBundle\Subscription\SubscribeBack\Handler\SubscribeBackHandlerInterface;
use SubscriptionBundle\Subscription\SubscribeBack\Subscriber;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommonFlowHandler
{
    /**
     * @var CommonFlowHandler
     */
    private $subscriber;

    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistAttemptRegistrator;

    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var DeviceDataProvider
     */
    private $deviceDataProvider;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var TokenGenerator
     */
    private $generator;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    /**
     * @var AfterSubscriptionProcessTracker
     */
    private $afterSubscriptionProcessTracker;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CommonFlowHandler constructor.
     *
     * @param Subscriber                      $subscriber
     * @param UserFactory                     $userFactory
     * @param BlacklistVoter                  $blacklistVoter
     * @param BlacklistAttemptRegistrator     $blacklistAttemptRegistrator
     * @param DeviceDataProvider              $deviceDataProvider
     * @param IdentificationDataStorage       $identificationDataStorage
     * @param SubscriptionPackProvider        $subscriptionPackProvider
     * @param SubscriptionExtractor           $subscriptionExtractor
     * @param UrlParamAppender                $urlParamAppender
     * @param UserRepository                  $userRepository
     * @param TokenGenerator                  $generator
     * @param CampaignExtractor               $campaignExtractor
     * @param AfterSubscriptionProcessTracker $afterSubscriptionProcessTracker
     * @param RouteProvider                   $routeProvider
     * @param ProcessResultSuccessChecker     $processResultSuccessChecker
     * @param ApiConnector                    $apiConnector
     * @param LoggerInterface                 $logger
     */
    public function __construct(
        Subscriber $subscriber,
        UserFactory $userFactory,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator,
        DeviceDataProvider $deviceDataProvider,
        IdentificationDataStorage $identificationDataStorage,
        SubscriptionPackProvider $subscriptionPackProvider,
        SubscriptionExtractor $subscriptionExtractor,
        UrlParamAppender $urlParamAppender,
        UserRepository $userRepository,
        TokenGenerator $generator,
        CampaignExtractor $campaignExtractor,
        AfterSubscriptionProcessTracker $afterSubscriptionProcessTracker,
        RouteProvider $routeProvider,
        ProcessResultSuccessChecker $processResultSuccessChecker,
        ApiConnector $apiConnector,
        LoggerInterface $logger
    )
    {
        $this->subscriber                      = $subscriber;
        $this->blacklistVoter                  = $blacklistVoter;
        $this->blacklistAttemptRegistrator     = $blacklistAttemptRegistrator;
        $this->userFactory                     = $userFactory;
        $this->deviceDataProvider              = $deviceDataProvider;
        $this->identificationDataStorage       = $identificationDataStorage;
        $this->subscriptionPackProvider        = $subscriptionPackProvider;
        $this->subscriptionExtractor           = $subscriptionExtractor;
        $this->urlParamAppender                = $urlParamAppender;
        $this->userRepository                  = $userRepository;
        $this->generator                       = $generator;
        $this->campaignExtractor               = $campaignExtractor;
        $this->afterSubscriptionProcessTracker = $afterSubscriptionProcessTracker;
        $this->routeProvider                   = $routeProvider;
        $this->resultSuccessChecker            = $processResultSuccessChecker;
        $this->crossSubscriptionApi            = $apiConnector;
        $this->logger                          = $logger;
    }

    /**
     * @param Request                       $request
     * @param CarrierInterface              $carrier
     * @param SubscribeBackHandlerInterface $handler
     *
     * @return RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     */
    public function process(
        Request $request,
        CarrierInterface $carrier,
        SubscribeBackHandlerInterface $handler
    ): Response
    {
        $this->logger->debug('Start subscribeBack process', [
            'carrier' => $carrier,
            'handler' => get_class($handler)
        ]);

        $msisdn           = $request->get('msisdn');
        $billingProcessId = $request->get('bf_process_id');
        $campaign         = $this->campaignExtractor->getCampaignFromSession($request->getSession());
        $redirectUrl      = $this->routeProvider->getLinkToHomepage();

        $user                = $this->userRepository->findOneByMsisdn($msisdn);
        $identificationToken = $this->identificationDataStorage->getIdentificationToken();

        if ($user && !$identificationToken) {
            $this->identificationDataStorage->setIdentificationToken($user->getIdentificationToken());
        }

        if (!$user) {
            $newToken = $this->generator->generateToken();
            $user     = $this->userFactory->create(
                $msisdn,
                $carrier,
                $request->getClientIp(),
                $newToken,
                $billingProcessId,
                $this->deviceDataProvider->get()
            );

            $this->identificationDataStorage->setIdentificationToken($newToken);

            $this->logger->debug('Create new user', ['user' => $user]);
        }

        $subscription = $this->subscriptionExtractor->getExistingSubscriptionForUser($user);
        if ($subscription) {
            $updatedUrl = $this->urlParamAppender->appendUrl($redirectUrl, [
                'err_handle' => 'already_subscribed'
            ]);

            return new RedirectResponse($updatedUrl);
        }

        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        if ($this->blacklistVoter->isPhoneNumberBlacklisted($msisdn)) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }

        try {
            /** @var Subscription $newSubscription */
            /** @var ProcessResult $result */
            list($newSubscription, $result) = $this->subscriber->subscribe($user, $subscriptionPack, $billingProcessId);

            $this->afterSubscriptionProcessTracker->track($result, $newSubscription, $handler, $campaign);

            if ($newSubscription->isSubscribed() && $this->resultSuccessChecker->isSuccessful($result)) {
                $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
            }

            $this->logger->debug('Create new subscription', ['subscription' => $subscription]);
            $this->logger->debug('Finish subscribeBack process');

            return new RedirectResponse($redirectUrl);
        } catch (\Exception $exception) {
            return new RedirectResponse($this->routeProvider->getLinkToWrongCarrierPage());
        }
    }
}