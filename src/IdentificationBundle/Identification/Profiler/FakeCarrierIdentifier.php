<?php

namespace IdentificationBundle\Identification\Profiler;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class FakeCarrierIdentifier extends DataCollector
{


    /**
     * Collects data for the given Request and Response.
     *
     * @param Request    $request A Request instance
     * @param Response   $response A Response instance
     * @param \Exception $exception An Exception instance
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {

    }

    /**
     * Returns the name of the collector.
     * @return string The collector name
     */
    public function getName()
    {
        return 'identification.fake_carrier_identifier';
    }

    public function reset()
    {
        // TODO: Implement reset() method.
    }
}