<?php

namespace DeviceDetectionBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class Identifier implements IdentifierInterface
{
    /**
     * @var RequestStack
     */
    protected $_request;

    /**
     * @inheritdoc
     */
    public function __construct(RequestStack $request)
    {
        $this->_request = $request->getCurrentRequest();
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        // Outside the handling of a request, $requestStack->getCurrentRequest() returns null.
        if(!is_null($this->_request)) {
            return $this->_request->headers->get('User-Agent');
        }
    }
}