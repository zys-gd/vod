<?php

namespace DeviceDetectionBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

interface IdentifierInterface
{
    /**
     * Identifier constructor.
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request);

    /**
     * Returns the identifier of the request.
     * @return mixed
     */
    public function get();
}