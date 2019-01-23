<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.01.19
 * Time: 16:57
 */

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Handler\HasConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @var UserFactory
     */
    private $userFactory;


    /**
     * ConsentPageSubscribeAction constructor.
     * @param CarrierRepositoryInterface    $carrierRepository
     * @param IdentificationHandlerProvider $identificationHandlerProvider
     * @param SubscriptionHandlerProvider   $subscriptionHandlerProvider
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        IdentificationHandlerProvider $identificationHandlerProvider,
        SubscriptionHandlerProvider $subscriptionHandlerProvider,
        UserFactory $userFactory
    )
    {
        $this->carrierRepository             = $carrierRepository;
        $this->identificationHandlerProvider = $identificationHandlerProvider;
        $this->subscriptionHandlerProvider   = $subscriptionHandlerProvider;
        $this->userFactory                   = $userFactory;
    }

    public function __invoke(Request $request, ISPData $ISPData)
    {

        $carrier = $this->carrierRepository->findOneByBillingId($ISPData->getCarrierId());

        $this->ensureConsentPageFlowIsAvailable($carrier);

        $subscriber = $this->subscriptionHandlerProvider->getSubscriber($carrier);

        if (!$subscriber instanceof HasCustomFlow) {
            throw new BadRequestHttpException('This action is available only for subscription `CustomFlow`');
        }

        // Not good idea tbh to change customFlow interface. Maybe should create separate one for such cases?
        return $subscriber->process($request, $request->getSession(), null);

    }

    /**
     * @param CarrierInterface $carrier
     */
    private function ensureConsentPageFlowIsAvailable(CarrierInterface $carrier): void
    {

        $handler = $this->identificationHandlerProvider->get($carrier);

        if (!$handler instanceof HasConsentPageFlow) {
            throw new BadRequestHttpException('This action is available only for identification `ConsentPageFlow`');
        }
    }


}