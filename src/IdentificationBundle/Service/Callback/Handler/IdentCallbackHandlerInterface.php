<?php

namespace IdentificationBundle\Service\Callback\Handler;


interface IdentCallbackHandlerInterface
{
    public function canHandle(int $carrierId): bool;

}