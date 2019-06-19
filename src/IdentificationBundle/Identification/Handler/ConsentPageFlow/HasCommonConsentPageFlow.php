<?php

namespace IdentificationBundle\Identification\Handler\ConsentPageFlow;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface HasConsentPageFlow
 */
interface HasCommonConsentPageFlow
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request): array;
}