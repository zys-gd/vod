<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 23/08/17
 * Time: 2:18 PM
 */

namespace SubscriptionBundle\Service\Logs;


use Symfony\Component\HttpFoundation\RequestStack;

class RequestProcessor
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * RequestProcessor constructor.
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request)
    {
        $this->requestStack = $request;
    }

    /**
     * @param array $record
     * @return array
     */
    public function processRecord(array $record)
    {
        $req = $this->requestStack->getCurrentRequest();
        if($req) {
            $record['extra']['client_ip'] = $req->getClientIp();
            $record['extra']['client_port'] = $req->getPort();
            $record['extra']['uri'] = $req->getUri();
            $record['extra']['query_string'] = $req->getQueryString();
            $record['extra']['method'] = $req->getMethod();
            $record['extra']['request'] = $req->request->all();
        }
        return $record;
    }
}