<?php

namespace SubscriptionBundle\Carriers\ZongPK\Subscribe;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\RouteProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ZongPKSubscribeHandler implements SubscriptionHandlerInterface, HasCommonFlow, HasCustomResponses
{
    /**
     * @var Notifier
     */
    private $notifier;
    /**
     * @var RouteProvider
     */
    private $routeProvider;


    /**
     * TelenorPKSubscribeHandler constructor.
     *
     * @param Notifier      $notifier
     * @param RouteProvider $routeProvider
     */
    public function __construct(Notifier $notifier, RouteProvider $routeProvider)
    {
        $this->notifier      = $notifier;
        $this->routeProvider = $routeProvider;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ZONG_PAKISTAN;
    }

    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        return [];
    }


    public function afterProcess(
        Subscription $subscription,
        ProcessResult $result
    )
    {
        // TODO: Implement afterProcess() method.
    }


    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     *
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
     *
     * @return Response|null
     */
    public function createResponseForExistingSubscription(Request $request, User $User, Subscription $subscription)
    {
        // TODO: Implement createResponseForExistingSubscription() method.
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return Response|null
     */
    public function createResponseBeforeSubscribeAttempt(Request $request, User $user)
    {
        // TODO: Implement createResponseBeforeSubscribeAttempt() method.
    }
}