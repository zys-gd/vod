<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 11:27
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface HasCustomFlow
{
    public function process(Request $request, SessionInterface $session, \IdentificationBundle\Entity\User $User = null): Response;
}