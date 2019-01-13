<?php

namespace IdentificationBundle\Callback\Handler;


interface IdentCallbackHandlerInterface
{
    public function canHandle(int $carrierId): bool;

}