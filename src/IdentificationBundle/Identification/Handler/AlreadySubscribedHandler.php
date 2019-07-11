<?php

namespace IdentificationBundle\Identification\Handler;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface AlreadySubscribedHandler
 */
interface AlreadySubscribedHandler
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool;

    /**
     * @param Request $request
     *
     * @return void
     */
    public function process(Request $request): void;
}