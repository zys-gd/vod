<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 10:30
 */

namespace SubscriptionBundle\Subscription\Subscribe\Controller;


use ExtrasBundle\Controller\Traits\ResponseTrait;
use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\User\Service\UserExtractor;
use SubscriptionBundle\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\CampaignConfirmation\Handler\CampaignConfirmationHandlerProvider;
use SubscriptionBundle\CampaignConfirmation\Handler\CustomPage;
use SubscriptionBundle\CAPTool\Subscription\Exception\CapToolAccessException;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimiter;
use SubscriptionBundle\Subscription\Subscribe\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Subscribe\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter;
use SubscriptionBundle\Subscription\Subscribe\Voter\BatchSubscriptionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @var SubscriptionHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var PostPaidHandler
     */
    private $postPaidHandler;
    /**
     * @var CampaignConfirmationHandlerProvider
     */
    private $campaignConfirmationHandlerProvider;
    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;
    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistDeducter;
    /**
     * @var BatchSubscriptionVoter
     */
    private $subscriptionVoter;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var \SubscriptionBundle\Subscription\Common\RouteProvider
     */
    private $subscriptionRouteProvider;


    /**
     * SubscribeAction constructor.
     *
     * @param \IdentificationBundle\User\Service\UserExtractor $userExtractor
     * @param CommonFlowHandler                                $commonFlowHandler
     * @param SubscriptionHandlerProvider                      $handlerProvider
     * @param BlacklistVoter                                   $blacklistVoter
     * @param IdentificationHandlerProvider                    $identificationHandlerProvider
     * @param CarrierRepositoryInterface                       $carrierRepository
     * @param PostPaidHandler                                            $postPaidHandler
     * @param CampaignConfirmationHandlerProvider                        $campaignConfirmationHandlerProvider
     * @param SubscriptionLimiter                                        $subscriptionLimiter
     * @param BlacklistAttemptRegistrator                                $blacklistDeducter
     * @param BatchSubscriptionVoter                                     $subscriptionVoter
     * @param RouteProvider                                              $routeProvider
     * @param \SubscriptionBundle\Subscription\Common\RouteProvider      $subscriptionRouteProvider
     */
    public function __construct(
        UserExtractor $userExtractor,
        CommonFlowHandler $commonFlowHandler,
        SubscriptionHandlerProvider $handlerProvider,
        BlacklistVoter $blacklistVoter,
        IdentificationHandlerProvider $identificationHandlerProvider,
        CarrierRepositoryInterface $carrierRepository,
        PostPaidHandler $postPaidHandler,
        CampaignConfirmationHandlerProvider $campaignConfirmationHandlerProvider,
        SubscriptionLimiter $subscriptionLimiter,
        BlacklistAttemptRegistrator $blacklistDeducter,
        BatchSubscriptionVoter $subscriptionVoter,
        RouteProvider $routeProvider,
        \SubscriptionBundle\Subscription\Common\RouteProvider $subscriptionRouteProvider
    )
    {

        $this->userExtractor                       = $userExtractor;
        $this->commonFlowHandler                   = $commonFlowHandler;
        $this->handlerProvider                     = $handlerProvider;
        $this->blacklistVoter                      = $blacklistVoter;
        $this->identificationHandlerProvider       = $identificationHandlerProvider;
        $this->carrierRepository                   = $carrierRepository;
        $this->postPaidHandler                     = $postPaidHandler;
        $this->campaignConfirmationHandlerProvider = $campaignConfirmationHandlerProvider;
        $this->subscriptionLimiter                 = $subscriptionLimiter;
        $this->blacklistDeducter                   = $blacklistDeducter;
        $this->subscriptionVoter                   = $subscriptionVoter;
        $this->routeProvider                       = $routeProvider;
        $this->subscriptionRouteProvider           = $subscriptionRouteProvider;
    }

    /**
     * @param Request            $request
     * @param IdentificationData $identificationData
     * @param ISPData            $ISPData
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function __invoke(Request $request, IdentificationData $identificationData, ISPData $ISPData)
    {
       /* if (!$this->subscriptionVoter->checkIfSubscriptionAllowed($request, $identificationData, $ISPData)) {
            return new RedirectResponse($this->routeProvider->getLinkToHomepage(['err_handle' => 'subscription_restricted']));
        }*/

        if ($this->postPaidHandler->isPostPaidRestricted()) {
            return new RedirectResponse($this->routeProvider->getLinkToHomepage(['err_handle' => 'postpaid_restricted']));
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
            $this->blacklistVoter->isUserBlacklisted($request->getSession()) ||
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
            return RedirectResponse::create($this->subscriptionRouteProvider->getActionIsNotAllowedUrl());

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
            return new RedirectResponse($this->routeProvider->getLinkToHomepage(['err_handle' => 'already_subscribed']));
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