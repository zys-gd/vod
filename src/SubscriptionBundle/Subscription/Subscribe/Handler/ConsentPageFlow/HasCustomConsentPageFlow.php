<?php

namespace SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow;

use IdentificationBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface HasCustomConsentPageFlow
 */
interface HasCustomConsentPageFlow
{
    /**
     * @param Request $request
     * @param User $user
     *
     * @return Response
     */
    public function process(Request $request, User $user): Response;
}