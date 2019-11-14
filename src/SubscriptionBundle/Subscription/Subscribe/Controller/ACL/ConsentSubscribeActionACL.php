<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 18.10.19
 * Time: 17:34
 */

namespace SubscriptionBundle\Subscription\Subscribe\Controller\ACL;

use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow as IdentConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsentSubscribeActionACL
{
    /**
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;
    /**
     * @var SubscriptionHandlerProvider
     */
    private $subscriptionHandlerProvider;
    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistAttemptRegistrator;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var RouteProvider
     */
    private $routeProvider;


    /**
     * ConsentSubscribeActionACL constructor.
     * @param IdentificationHandlerProvider $identificationHandlerProvider
     * @param SubscriptionHandlerProvider   $subscriptionHandlerProvider
     * @param BlacklistVoter                $blacklistVoter
     * @param BlacklistAttemptRegistrator   $blacklistAttemptRegistrator
     * @param CarrierRepositoryInterface    $carrierRepository
     * @param RouteProvider                 $routeProvider
     */
    public function __construct(
        IdentificationHandlerProvider $identificationHandlerProvider,
        SubscriptionHandlerProvider $subscriptionHandlerProvider,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator,
        CarrierRepositoryInterface $carrierRepository,
        RouteProvider $routeProvider
    )
    {
        $this->identificationHandlerProvider = $identificationHandlerProvider;
        $this->subscriptionHandlerProvider   = $subscriptionHandlerProvider;
        $this->blacklistVoter                = $blacklistVoter;
        $this->blacklistAttemptRegistrator   = $blacklistAttemptRegistrator;
        $this->carrierRepository             = $carrierRepository;
        $this->routeProvider                 = $routeProvider;
    }

    public function checkIfActionIsAllowed(Request $request, ISPData $ISPData, IdentificationData $identificationData): ?Response
    {
        $carrierId = $ISPData->getCarrierId();
        $carrier   = $this->carrierRepository->findOneByBillingId($carrierId);

        $identificationHandler = $this->identificationHandlerProvider->get($carrier);
        if (!$identificationHandler instanceof IdentConsentPageFlow) {
            return new RedirectResponse(
                $this->routeProvider->getLinkToHomepage([
                    'err_handle' => 'consent_not_supported_by_identification_handler'
                ])
            );
        }

        $subscriptionHandler = $this->subscriptionHandlerProvider->getSubscriber($carrier);
        if (!$subscriptionHandler instanceof HasConsentPageFlow) {
            return new RedirectResponse(
                $this->routeProvider->getLinkToHomepage([
                    'err_handle' => 'consent_not_supported_by_subscription_handler'
                ])
            );
        }


        if ($this->blacklistVoter->isUserBlacklisted($request->getSession())) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }

        $identificationToken = $identificationData->getIdentificationToken();
        if (!$this->blacklistAttemptRegistrator->registerSubscriptionAttempt($identificationToken, $carrierId)) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }

        return null;
    }

}