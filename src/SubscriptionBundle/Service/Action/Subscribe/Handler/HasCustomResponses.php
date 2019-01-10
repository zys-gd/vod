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
use UserBundle\Entity\BillableUser;

interface HasCustomResponses
{
    /**
     * @param Request      $request
     * @param BillableUser $billableUser
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForSuccessfulSubscribe(Request $request, BillableUser $billableUser, Subscription $subscription);


    /**
     * @param Request      $request
     * @param BillableUser $billableUser
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForExistingSubscription(Request $request, BillableUser $billableUser, Subscription $subscription);
}