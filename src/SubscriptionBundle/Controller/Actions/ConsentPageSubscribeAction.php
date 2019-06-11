<?php

namespace SubscriptionBundle\Controller\Actions;

use Doctrine\ORM\NonUniqueResultException;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\DTO\{IdentificationData, ISPData};
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Identification\Handler\HasConsentPageFlow as IdentConsentPageFlow;
use SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Service\Action\Subscribe\Consent\ConsentFlowHandler;
use SubscriptionBundle\Service\Action\Subscribe\Handler\ConsentPageFlow\{HasConsentPageFlow, HasCustomConsentPageFlow};
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @var UserExtractor
     */
    private $userExtractor;

    /**
     * @var ConsentFlowHandler
     */
    private $consentFlowHandler;

    /**
     * ConsentPageSubscribeAction constructor
     *
     * @param CarrierRepositoryInterface $carrierRepository
     * @param IdentificationHandlerProvider $identificationHandlerProvider
     * @param SubscriptionHandlerProvider $subscriptionHandlerProvider
     * @param UserExtractor $userExtractor
     * @param ConsentFlowHandler $consentFlowHandler
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        IdentificationHandlerProvider $identificationHandlerProvider,
        SubscriptionHandlerProvider $subscriptionHandlerProvider,
        UserExtractor $userExtractor,
        ConsentFlowHandler $consentFlowHandler
    ) {
        $this->carrierRepository = $carrierRepository;
        $this->identificationHandlerProvider = $identificationHandlerProvider;
        $this->subscriptionHandlerProvider = $subscriptionHandlerProvider;
        $this->userExtractor = $userExtractor;
        $this->consentFlowHandler = $consentFlowHandler;
    }

    /**
     * @param Request $request
     * @param IdentificationData $identificationData
     * @param ISPData $ISPData
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     * @throws ActiveSubscriptionPackNotFound
     * @throws ExistingSubscriptionException
     */
    public function __invoke(Request $request, IdentificationData $identificationData, ISPData $ISPData)
    {
        $carrier = $this->carrierRepository->findOneByBillingId($ISPData->getCarrierId());
        $user = $this->userExtractor->getUserByIdentificationData($identificationData);

        $this->ensureConsentPageFlowIsAvailable($carrier);

        $subscriber = $this->subscriptionHandlerProvider->getSubscriber($carrier);

        if (!$subscriber instanceof HasConsentPageFlow) {
            throw new BadRequestHttpException('This action is available only for subscription `ConsentPageFlow`');
        }

        if ($subscriber instanceof HasCustomConsentPageFlow) {
            return $subscriber->process($request, $user);
        } else {
            return $this->consentFlowHandler->process($request, $user, $subscriber);
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