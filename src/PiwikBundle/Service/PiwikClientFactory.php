<?php


namespace PiwikBundle\Service;


use PiwikBundle\Api\ClientAbstract;
use PiwikBundle\Api\JsClient;

class PiwikClientFactory
{
    /**
     * @var bool
     */
    private $jsClientEnabled;
    /**
     * @var ClientAbstract
     */
    private $phpClient;
    /**
     * @var JsClient
     */
    private $jsClient;

    /**
     * PiwikClientFactory constructor.
     *
     * @param bool           $jsClientEnabled
     * @param ClientAbstract $phpClient
     * @param JsClient       $jsClient
     */
    public function __construct(bool $jsClientEnabled, ClientAbstract $phpClient, JsClient $jsClient)
    {
        $this->jsClientEnabled = $jsClientEnabled;
        $this->phpClient       = $phpClient;
        $this->jsClient        = $jsClient;
    }

    /**
     * @return ClientAbstract
     */
    public function getPiwikClient(): ClientAbstract
    {
        if ($this->jsClientEnabled === true) {
            return $this->jsClient;
        }
        return $this->phpClient;
    }
}