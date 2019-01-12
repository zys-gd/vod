<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.05.18
 * Time: 10:02
 */

namespace IdentificationBundle\Profiler;


use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class IdentDataCollector extends DataCollector
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
        $session                        = $request->getSession();
        $this->data['current_identity'] = [
            'isp'            => IdentificationFlowDataExtractor::extractIspDetectionData($session),
            'identification' => IdentificationFlowDataExtractor::extractIdentificationData($session),
            'wifi_flow'      => $session->get('is_wifi_flow')
        ];

    }

    public function getCurrentIdentity()
    {
        return $this->data['current_identity'];
    }


    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'identification.ident_data_collector';
    }


    public function reset()
    {
        $this->data['current_identity'] = null;
    }
}