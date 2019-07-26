<?php

namespace SubscriptionBundle\Subscription\SubscribeBack;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractSubscribeBackHandler
{
    abstract function canHandle(Request $request): bool;

    abstract public function handleRequest(Request $request): RedirectResponse;
}