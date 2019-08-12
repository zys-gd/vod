<?php

namespace IdentificationBundle\Identification\Handler\PassthroughFlow;

use Symfony\Component\HttpFoundation\Request;

interface HasPassthroughFlow
{
    public function getAdditionalIdentificationParams(Request $request): array;
}