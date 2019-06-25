<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 10:30
 */

namespace SubscriptionBundle\Controller\Actions;


use ExtrasBundle\Utils\UrlParamAppender;
use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Service\Action\Subscribe\Common\BlacklistVoter;
use SubscriptionBundle\Service\Action\Subscribe\Common\CommonFlowHandler;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Service\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\Service\CampaignConfirmation\Handler\CampaignConfirmationHandlerProvider;
use SubscriptionBundle\Service\CampaignConfirmation\Handler\CustomPage;
use SubscriptionBundle\Service\CAPTool\Exception\CapToolAccessException;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimiter;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimiterInterface;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimitNotifier;
use SubscriptionBundle\Service\SubscriptionVoter\BatchSubscriptionVoter;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Router;

class SubscribeAction extends AbstractController
{
    use ResponseTrait;

    /**
     * @var UserExtractor
     */
    private $userExtractor;
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;
    /**
     * @var SubscriptionHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var string
     */
    private $defaultRedirectUrl;
    /**
     * @var PostPaidHandler
     */
    private $postPaidHandler;
    /**
     * @var CampaignConfirmationHandlerProvider
     */
    private $campaignConfirmationHandlerProvider;
    /**
     * @var BatchSubscriptionVoter
     */
    private $subscriptionVoter;
    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;
    /**
     * @var SubscriptionLimitNotifier
     */
    private $subscriptionLimitNotifier;
    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistDeducter;

    /**
     * SubscribeAction constructor.
     *
     * @param UserExtractor                       $userExtractor
     * @param CommonFlowHandler                   $commonFlowHandler
     * @param Router                              $router
     * @param LoggerInterface                     $logger
     * @param UrlParamAppender                    $urlParamAppender
     * @param SubscriptionHandlerProvider         $handlerProvider
     * @param BlacklistVoter                      $blacklistVoter
     * @param IdentificationDataStorage           $identificationDataStorage
     * @param IdentificationHandlerProvider       $identificationHandlerProvider
     * @param CarrierRepositoryInterface          $carrierRepository
     * @param string                              $defaultRedirectUrl
     * @param PostPaidHandler                     $postPaidHandler
     * @param CampaignConfirmationHandlerProvider $campaignConfirmationHandlerProvider
     * @param SubscriptionLimiter                 $subscriptionLimiter
     * @param SubscriptionLimitNotifier           $subscriptionLimitNotifier
     * @param BlacklistAttemptRegistrator         $blacklistDeducter
     * @param BatchSubscriptionVoter              $subscriptionVoter
     */
    public function __construct(
        UserExtractor $userExtractor,
        CommonFlowHandler $commonFlowHandler,
        Router $router,
        LoggerInterface $logger,
        UrlParamAppender $urlParamAppender,
        SubscriptionHandlerProvider $handlerProvider,
        BlacklistVoter $blacklistVoter,
        IdentificationDataStorage $identificationDataStorage,
        IdentificationHandlerProvider $identificationHandlerProvider,
        CarrierRepositoryInterface $carrierRepository,
        string $defaultRedirectUrl,
        PostPaidHandler $postPaidHandler,
        CampaignConfirmationHandlerProvider $campaignConfirmationHandlerProvider,
        SubscriptionLimiter $subscriptionLimiter,
        SubscriptionLimitNotifier $subscriptionLimitNotifier,
        BlacklistAttemptRegistrator $blacklistDeducter,
        BatchSubscriptionVoter $subscriptionVoter
    )
    {
        $this->userExtractor                       = $userExtractor;
        $this->commonFlowHandler                   = $commonFlowHandler;
        $this->router                              = $router;
        $this->logger                              = $logger;
        $this->urlParamAppender                    = $urlParamAppender;
        $this->handlerProvider                     = $handlerProvider;
        $this->blacklistVoter                      = $blacklistVoter;
        $this->identificationDataStorage           = $identificationDataStorage;
        $this->identificationHandlerProvider       = $identificationHandlerProvider;
        $this->carrierRepository                   = $carrierRepository;
        $this->defaultRedirectUrl                  = $defaultRedirectUrl;
        $this->postPaidHandler                     = $postPaidHandler;
        $this->campaignConfirmationHandlerProvider = $campaignConfirmationHandlerProvider;
        $this->subscriptionVoter                   = $subscriptionVoter;
        $this->subscriptionLimiter                 = $subscriptionLimiter;
        $this->subscriptionLimitNotifier           = $subscriptionLimitNotifier;
        $this->blacklistDeducter                   = $blacklistDeducter;
    }

    /**
     * @param Request            $request
     * @param IdentificationData $identificationData
     * @param ISPData            $ISPData
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function __invoke(Request $request, IdentificationData $identificationData, ISPData $ISPData)
    {
        if (!$this->subscriptionVoter->checkIfSubscriptionAllowed($request, $identificationData, $ISPData)) {
            return new RedirectResponse($this->generateUrl('index', ['err_handle' => 'subscription_restricted']));
        }

        if ($this->postPaidHandler->isPostPaidRestricted()) {
            return new RedirectResponse($this->generateUrl('index', ['err_handle' => 'postpaid_restricted']));
        }

        $campaignConfirmationHandler = $this->campaignConfirmationHandlerProvider->provideHandler($request->getSession());
        if ($campaignConfirmationHandler instanceof CustomPage) {
            $result = $campaignConfirmationHandler->proceedCustomPage($request);
            if ($result instanceof RedirectResponse) {
                return $result;
            }
        }

        /*if ($result = $this->handleRequestByLegacyService($request)) {
            return $result;
        }*/

        $this->ensureNotConsentPageFlow($ISPData->getCarrierId());

        if (
            $this->blacklistVoter->isInBlacklist($request->getSession()) ||
            !$this->blacklistDeducter->registerSubscriptionAttempt(
                $identificationData->getIdentificationToken(),
                (int)$ISPData->getCarrierId()
            )
        ) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }

        $user = $this->userExtractor->getUserByIdentificationData($identificationData);


        try {
            $this->subscriptionLimiter->ensureCapIsNotReached($request->getSession());
        } catch (CapToolAccessException $exception) {
            return RedirectResponse::create($this->defaultRedirectUrl);

        }

        if ($this->subscriptionLimiter->need2BeLimited($user)) {
            $this->subscriptionLimiter->reserveSlotForSubscription($request->getSession());
        }

        try {

            $subscriber = $this->handlerProvider->getSubscriber($user->getCarrier());
            if ($subscriber instanceof HasCustomFlow) {
                return $subscriber->process($request, $request->getSession(), $user);
            } else {
                return $this->commonFlowHandler->process($request, $user);
            }
        } catch (ExistingSubscriptionException $exception) {
            return new RedirectResponse($this->generateUrl('index', ['err_handle' => 'already_subscribed']));
        }


    }

    private function ensureNotConsentPageFlow(int $carrierId): void
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);

        $handler = $this->identificationHandlerProvider->get($carrier);

        if ($handler instanceof HasCommonConsentPageFlow) {
            throw new BadRequestHttpException('This action is not available for `ConsentPageFlow`');
        }

    }

    /*private function handleRequestByLegacyService(Request $request)
    {
        if ($this->mobimindService->isMobimind($request)) {
            return $this->mobimindService->processRequest($request);
        }

        if ($this->mobilifeService->isMobilife($request)) {
            return $this->mobilifeService->processRequest($request);
        }

        $carrier = $request->get('carrier');
        if ($this->megasystCarrierChecker->isMegasystCarrierId($carrier)) {

            if ($phone = $request->get('phone')) {
                return new JsonResponse($this->megasystPhoneChecker->checkPhone($phone, $carrier));
            } else {
                return new Response(200);
            }
        }


    }*/


}