<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.08.19
 * Time: 17:04
 */

namespace SubscriptionBundle\Piwik\Profiler;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class TraceableSenderCollector implements DataCollectorInterface
{
    /**
     * @var TraceableSender
     */
    private $traceableSender;

    private $data = [
        'calls' => []
    ];

    /**
     * TraceCollector constructor.
     * @param TraceableSender $traceableSender
     */
    public function __construct(TraceableSender $traceableSender)
    {
        $this->traceableSender = $traceableSender;
    }


    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'subscription.piwik_events_collector';
    }

    public function reset()
    {
        $this->traceableSender->reset();
    }

    /**
     * Collects data for the given Request and Response.
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['calls'] = $this->traceableSender->getCalls();
    }

    public function getCalls(): array
    {
        return $this->data['calls'];
    }
}