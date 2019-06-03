<?php


namespace PiwikBundle\Service;


use PiwikBundle\Api\ClientAbstract;

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
     * @var ClientAbstract
     */
    private $jsClient;

    /**
     * PiwikClientFactory constructor.
     *
     * @param bool           $jsClientEnabled
     * @param ClientAbstract $phpClient
     * @param ClientAbstract $jsClient
     */
    public function __construct(bool $jsClientEnabled, ClientAbstract $phpClient, ClientAbstract $jsClient)
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