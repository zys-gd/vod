<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 18.10.19
 * Time: 15:39
 */

namespace SubscriptionBundle\Subscription\Subscribe\Controller\ACL;


use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\CampaignConfirmation\Handler\CampaignConfirmationHandlerProvider;
use SubscriptionBundle\CampaignConfirmation\Handler\CustomPage;
use SubscriptionBundle\CAPTool\Common\CAPToolRedirectUrlResolver;
use SubscriptionBundle\CAPTool\Subscription\Exception\CapToolAccessException;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimiter;
use SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SubscribeActionACL
{
    /**
     * @var PostPaidHandler
     */
    private $postPaidHandler;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var CampaignConfirmationHandlerProvider
     */
    private $campaignConfirmationHandlerProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistAttemptRegistrator;
    /**
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;
    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;
    /**
     * @var CAPToolRedirectUrlResolver
     */
    private $CAPToolRedirectUrlResolver;


    /**
     * SubscribeActionACL constructor.
     * @param PostPaidHandler                     $postPaidHandler
     * @param RouteProvider                       $routeProvider
     * @param CampaignConfirmationHandlerProvider $campaignConfirmationHandlerProvider
     * @param CarrierRepositoryInterface          $carrierRepository
     * @param BlacklistVoter                      $blacklistVoter
     * @param BlacklistAttemptRegistrator         $blacklistAttemptRegistrator
     * @param IdentificationHandlerProvider       $identificationHandlerProvider
     * @param SubscriptionLimiter                 $subscriptionLimiter
     * @param CAPToolRedirectUrlResolver          $CAPToolRedirectUrlResolver
     */
    public function __construct(
        PostPaidHandler $postPaidHandler,
        RouteProvider $routeProvider,
        CampaignConfirmationHandlerProvider $campaignConfirmationHandlerProvider,
        CarrierRepositoryInterface $carrierRepository,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator,
        IdentificationHandlerProvider $identificationHandlerProvider,
        SubscriptionLimiter $subscriptionLimiter,
        CAPToolRedirectUrlResolver $CAPToolRedirectUrlResolver
    )
    {
        $this->postPaidHandler                     = $postPaidHandler;
        $this->routeProvider                       = $routeProvider;
        $this->campaignConfirmationHandlerProvider = $campaignConfirmationHandlerProvider;
        $this->carrierRepository                   = $carrierRepository;
        $this->blacklistVoter                      = $blacklistVoter;
        $this->blacklistAttemptRegistrator         = $blacklistAttemptRegistrator;
        $this->identificationHandlerProvider       = $identificationHandlerProvider;
        $this->subscriptionLimiter                 = $subscriptionLimiter;
        $this->CAPToolRedirectUrlResolver          = $CAPToolRedirectUrlResolver;
    }

    public function checkIfActionIsAllowed(Request $request, ISPData $ISPData, IdentificationData $identificationData): ?Response
    {

        /*if (!$this->subscriptionVoter->checkIfSubscriptionAllowed($request, $identificationData, $ISPData)) {
            return new RedirectResponse($this->routeProvider->getLinkToHomepage(['err_handle' => 'subscription_restricted']));
        }*/

        if ($this->postPaidHandler->isPostPaidRestricted()) {
            return new RedirectResponse(
                $this->routeProvider->getLinkToHomepage(['err_handle' => 'postpaid_restricted'])
            );
        }

        $campaignConfirmationHandler = $this->campaignConfirmationHandlerProvider->provideHandler($request->getSession());
        if ($campaignConfirmationHandler instanceof CustomPage) {
            $result = $campaignConfirmationHandler->proceedCustomPage($request);
            if ($result instanceof RedirectResponse) {
                return $result;
            }
        }

        try {
            $this->ensureNotConsentPageFlow($ISPData->getCarrierId());
        } catch (BadRequestHttpException $exception) {
            return new RedirectResponse(
                $this->routeProvider->getLinkToHomepage(['err_handle' => 'not_available_for_consent_flow'])
            );
        }

        if (
            $this->blacklistVoter->isUserBlacklisted($request->getSession()) ||
            !$this->blacklistAttemptRegistrator->registerSubscriptionAttempt(
                $identificationData->getIdentificationToken(),
                (int)$ISPData->getCarrierId()
            )
        ) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }


        try {
            $this->subscriptionLimiter->ensureCapIsNotReached($request->getSession());
        } catch (CapToolAccessException $exception) {
            $url = $this->CAPToolRedirectUrlResolver->resolveUrl($exception);
            return RedirectResponse::create($url);
        }

        return null;
    }


    private function ensureNotConsentPageFlow(int $carrierId): void
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $handler = $this->identificationHandlerProvider->get($carrier);

        if ($handler instanceof HasCommonConsentPageFlow) {
            throw new BadRequestHttpException('This action is not available for `ConsentPageFlow`');
        }
    }

}