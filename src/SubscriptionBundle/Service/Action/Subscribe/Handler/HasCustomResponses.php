<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 11:28
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SubscriptionBundle\Entity\Subscription;
use IdentificationBundle\Entity\User;

interface HasCustomResponses
{
    /**
     * @param Request      $request
     * @param User $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForSuccessfulSubscribe(Request $request, User $User, Subscription $subscription);


    /**
     * @param Request      $request
     * @param User $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForExistingSubscription(Request $request, User $User, Subscription $subscription);
}