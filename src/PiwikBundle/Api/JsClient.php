<?php

namespace PiwikBundle\Api;

/**
 * Class JsClient
 *
 * @package Playwing\PiwikBundle\Api
 */
class JsClient extends ClientAbstract
{
    /**
     * @var boolean
     */
    private $enableLinkTracking;

    /**
     * @var string
     */
    private $queryString;

    public function __construct($idSite, $host, $https, $tokenAuth)
    {
        parent::__construct($idSite, $host, $https, $tokenAuth);

        $this->ip = null;
        $this->userAgent = null;
        $this->acceptLanguage = null;
    }

    /**
     * @param boolean $enableLinkTracking
     */
    public function setEnableLinkTracking($enableLinkTracking)
    {
        $this->enableLinkTracking = $enableLinkTracking;
    }

    /**
     * @return boolean
     */
    public function getEnableLinkTracking()
    {
        return $this->enableLinkTracking;
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * @inheritdoc
     */
    protected function sendRequest($url, $method = 'GET', $data = null, $force = false)
    {
        $url .= (!empty($this->userAgent) ? ('&ua=' . urlencode($this->userAgent)) : '');
        $url .= (!empty($this->acceptLanguage) ? ('&lang=' . urlencode($this->acceptLanguage)) : '');

        $this->queryString = parse_url($url, PHP_URL_QUERY);

        return true;
    }
}