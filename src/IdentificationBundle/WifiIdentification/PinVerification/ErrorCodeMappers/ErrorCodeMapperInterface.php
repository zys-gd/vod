<?php

namespace IdentificationBundle\WifiIdentification\PinVerification\ErrorCodeMappers;

interface ErrorCodeMapperInterface
{
    public function canHandle(int $billingCarrierId): bool;

    public function map(int $billingResponseCode): int;
}