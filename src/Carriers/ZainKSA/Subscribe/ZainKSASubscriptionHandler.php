<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.10.19
 * Time: 12:40
 */

namespace Carriers\ZainKSA\Subscribe;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ZainKSASubscriptionHandler implements SubscriptionHandlerInterface, HasCustomResponses
{

    public function canHandle(\CommonDataBundle\Entity\Interfaces\CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ZAIN_SAUDI_ARABIA;
    }

    /**
     * @param Request $request
     * @param User    $user
     * @return Response|null
     */
    public function createResponseBeforeSubscribeAttempt(Request $request, User $user)
    {
        if (preg_match('/966831\d+/', $user->getIdentifier())) {
            return new RedirectResponse('google.com');
        }
    }

    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForSuccessfulSubscribe(Request $request, User $User, Subscription $subscription)
    {
        // TODO: Implement createResponseForSuccessfulSubscribe() method.
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