<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 11:27
 */

namespace SubscriptionBundle\Subscription\Subscribe\Handler;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface HasCustomFlow
{
    public function process(Request $request, SessionInterface $session, \IdentificationBundle\Entity\User $User = null): Response;

    public function updateSubscribeConstraint(array $campaignData): void;
}