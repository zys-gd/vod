<?php

namespace IdentificationBundle\Identification\Handler\PassthroughFlow;

use Symfony\Component\HttpFoundation\Request;

interface HasPassthroughFlow
{
    public function isCommonFlowShouldBeUsed(Request $request): bool;

}