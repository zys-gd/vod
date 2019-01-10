<?php

namespace SubscriptionBundle\Service\Callback\Impl;

use Symfony\Component\HttpFoundation\Request;

interface CarrierCallbackHandlerInterface
{
    public function canHandle(Request $request, int $carrierId): bool;

}