<?php

namespace SubscriptionBundle\Subscription\Subscribe\Controller;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Identification\DTO\{IdentificationData, ISPData};
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow as IdentConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\User\Service\UserExtractor;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\Subscription\Subscribe\Consent\ConsentFlowHandler;
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\{HasConsentPageFlow, HasCustomConsentPageFlow};
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;

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
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;

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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistAttemptRegistrator;

    /**
     * @var \SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter
     */
    private $blacklistVoter;

    /**
     * ConsentPageSubscribeAction constructor
     *
     * @param CarrierRepositoryInterface                                        $carrierRepository
     * @param IdentificationHandlerProvider                                     $identificationHandlerProvider
     * @param SubscriptionHandlerProvider                                       $subscriptionHandlerProvider
     * @param \IdentificationBundle\User\Service\UserExtractor                  $userExtractor
     * @param ConsentFlowHandler                                                $consentFlowHandler
     * @param RouterInterface                                                   $router
     * @param \SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter $blacklistVoter
     * @param BlacklistAttemptRegistrator                                       $blacklistAttemptRegistrator
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        IdentificationHandlerProvider $identificationHandlerProvider,
        SubscriptionHandlerProvider $subscriptionHandlerProvider,
        UserExtractor $userExtractor,
        ConsentFlowHandler $consentFlowHandler,
        RouterInterface $router,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator
    )
    {
        $this->carrierRepository             = $carrierRepository;
        $this->identificationHandlerProvider = $identificationHandlerProvider;
        $this->subscriptionHandlerProvider   = $subscriptionHandlerProvider;
        $this->userExtractor                 = $userExtractor;
        $this->consentFlowHandler            = $consentFlowHandler;
        $this->router                        = $router;
        $this->blacklistVoter                = $blacklistVoter;
        $this->blacklistAttemptRegistrator   = $blacklistAttemptRegistrator;
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
    public function __invoke(Request $request, IdentificationData $identificationData, ISPData $ispData)
    {
        $carrierId           = $ispData->getCarrierId();
        $identificationToken = $identificationData->getIdentificationToken();

        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $user    = $this->userExtractor->getUserByIdentificationData($identificationData);

        $this->ensureConsentPageFlowIsAvailable($carrier);

        $subscriber = $this->subscriptionHandlerProvider->getSubscriber($carrier);

        if (!$subscriber instanceof HasConsentPageFlow) {
            throw new BadRequestHttpException('This action is available only for subscription `ConsentPageFlow`');
        }

        if ($this->blacklistVoter->isUserBlacklisted($request->getSession())
            || !$this->blacklistAttemptRegistrator->registerSubscriptionAttempt($identificationToken, (int)$carrierId)
        ) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }

        try {
            if ($subscriber instanceof HasCustomConsentPageFlow) {
                return $subscriber->process($request, $user);
            } else {
                return $this->consentFlowHandler->process($request, $user, $subscriber);
            }
        } catch (SubscribingProcessException $exception) {
            return $subscriber->getSubscriptionErrorResponse($exception);
        } catch (\Exception $exception) {
            return new RedirectResponse($this->router->generate('whoops'));
        }
    }

    /**
     * @param CarrierInterface $carrier
     */
    private function ensureConsentPageFlowIsAvailable(CarrierInterface $carrier): void
    {
        $handler = $this->identificationHandlerProvider->get($carrier);

        if (!$handler instanceof IdentConsentPageFlow) {
            throw new BadRequestHttpException('This action is available only for identification `ConsentPageFlow`');
        }
    }
}