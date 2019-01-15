<?php

namespace IdentificationBundle\Profiler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class FakeActions extends DataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['status_color'] = 'red';
        $this->data['status'] = 'red';
        return;
    }
    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'identification.fake_actions';
    }

    public function reset()
    {
        // TODO: Implement reset() method.
    }
}