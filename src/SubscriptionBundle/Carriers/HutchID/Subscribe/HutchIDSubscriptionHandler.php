<?php

namespace SubscriptionBundle\Carriers\HutchID\Subscribe;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\RouteProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class HutchIDSubscriptionHandler implements SubscriptionHandlerInterface, HasCommonFlow, HasCustomResponses
{
    /**
     * @var RouteProvider
     */
    private $routeProvider;

    /**
     * HutchIDSubscriptionHandler constructor.
     */
    public function __construct(RouteProvider $routeProvider)
    {
        $this->routeProvider = $routeProvider;
    }


    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::HUTCH_INDONESIA;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $user): array
    {
        return [];
    }

    /**
     * @param Subscription  $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void
    {

    }

    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForSuccessfulSubscribe(Request $request, User $User, Subscription $subscription)
    {

        if ($subscription->getError() === 'subscribed_to_another_service') {
            return new RedirectResponse($this->routeProvider->getLinkToHomepage(['err_handle' => 'already_subscribed_on_another_service']));
        }
    }

    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForExistingSubscription(Request $request, User $User, Subscription $subscription)
    {

    }
}