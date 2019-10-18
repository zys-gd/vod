<?php

namespace SubscriptionBundle\Subscription\Subscribe\Controller;

use IdentificationBundle\Identification\DTO\{IdentificationData, ISPData};
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\User\Service\UserExtractor;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\Subscription\Subscribe\Consent\ConsentFlowHandler;
use SubscriptionBundle\Subscription\Subscribe\Controller\ACL\ConsentSubscribeActionACL;
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\{HasCustomConsentPageFlow};
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};

/**
 * Class ConsentPageSubscribeAction
 */
class ConsentPageSubscribeAction
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var SubscriptionHandlerProvider
     */
    private $subscriptionHandlerProvider;

    /**
     * @var \IdentificationBundle\User\Service\UserExtractor
     */
    private $userExtractor;

    /**
     * @var ConsentFlowHandler
     */
    private $consentFlowHandler;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var ConsentSubscribeActionACL
     */
    private $ACL;


    /**
     * ConsentPageSubscribeAction constructor
     *
     * @param CarrierRepositoryInterface                       $carrierRepository
     * @param SubscriptionHandlerProvider                      $subscriptionHandlerProvider
     * @param \IdentificationBundle\User\Service\UserExtractor $userExtractor
     * @param ConsentFlowHandler                               $consentFlowHandler
     * @param RouteProvider                                    $routeProvider
     * @param ConsentSubscribeActionACL                        $ACL
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        SubscriptionHandlerProvider $subscriptionHandlerProvider,
        UserExtractor $userExtractor,
        ConsentFlowHandler $consentFlowHandler,
        RouteProvider $routeProvider,
        ConsentSubscribeActionACL $ACL
    )
    {
        $this->carrierRepository           = $carrierRepository;
        $this->subscriptionHandlerProvider = $subscriptionHandlerProvider;
        $this->userExtractor               = $userExtractor;
        $this->consentFlowHandler          = $consentFlowHandler;
        $this->routeProvider               = $routeProvider;
        $this->ACL                         = $ACL;
    }


    /**
     * @param Request            $request
     * @param IdentificationData $identificationData
     * @param ISPData            $ispData
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function __invoke(Request $request, IdentificationData $identificationData, ISPData $ispData): Response
    {
        if ($aclOverride = $this->ACL->checkIfActionIsAllowed($request, $ispData, $identificationData)) {
            return $aclOverride;
        }

        $carrierId  = $ispData->getCarrierId();
        $carrier    = $this->carrierRepository->findOneByBillingId($carrierId);
        $user       = $this->userExtractor->getUserByIdentificationData($identificationData);
        $subscriber = $this->subscriptionHandlerProvider->getSubscriber($carrier);

        try {
            if ($subscriber instanceof HasCustomConsentPageFlow) {
                return $subscriber->process($request, $user);
            } else {
                return $this->consentFlowHandler->process($request, $user, $subscriber);
            }
        } catch (SubscribingProcessException $exception) {
            return $subscriber->getSubscriptionErrorResponse($exception);
        } catch (\Exception $exception) {
            return new RedirectResponse($this->routeProvider->getLinkToWrongCarrierPage());
        }
    }

}