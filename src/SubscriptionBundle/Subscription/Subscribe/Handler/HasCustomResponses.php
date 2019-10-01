<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 11:28
 */

namespace SubscriptionBundle\Subscription\Subscribe\Handler;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface HasCustomResponses
{
    /**
     * @param Request $request
     * @param User    $user
     * @return Response|null
     */
    public function createResponseBeforeSubscribeAttempt(Request $request, User $user);

    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForSuccessfulSubscribe(Request $request, User $User, Subscription $subscription);


    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForExistingSubscription(Request $request, User $User, Subscription $subscription);
}