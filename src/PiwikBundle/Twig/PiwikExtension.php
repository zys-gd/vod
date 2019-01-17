<?php

namespace PiwikBundle\Twig;

use PiwikBundle\Api\JsClient;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class PiwikExtension
 *
 * @package Playwing\PiwikBundle\Twig
 */
class PiwikExtension extends Twig_Extension
{
    /**
     * @var JsClient
     */
    protected $jsClient;

    /**
     * @var boolean
     */
    protected $jsEnabled;

    /**
     * PiwikExtension constructor.
     *
     * @param JsClient $jsClient
     * @param boolean $jsEnabled
     */
    function __construct(JsClient $jsClient, $jsEnabled)
    {
        $this->jsClient = $jsClient;
        $this->jsEnabled = $jsEnabled;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getApiTrackerUrl', [$this, 'getApiTrackerUrl']),
            new Twig_SimpleFunction('getApiJsUrl', [$this, 'getApiJsUrl']),
            new Twig_SimpleFunction('getApiImageUrl', [$this, 'getApiImageUrl']),
            new Twig_SimpleFunction('getEnableLinkTracking', [$this, 'getEnableLinkTracking']),
            new Twig_SimpleFunction('getQueryString', [$this, 'getQueryString']),
            new Twig_SimpleFunction('jsEnabled', [$this, 'jsEnabled']),
        ];
    }

    /**
     * @return string
     */
    public function getApiTrackerUrl()
    {
        return $this->jsClient->getApiUrl() . '/piwik.php';
    }

    /**
     * @return string
     */
    public function getApiJsUrl()
    {
        return $this->jsClient->getApiUrl() . '/piwik.js';
    }

    /**
     * @return string
     */
    public function getApiImageUrl()
    {
        return $this->jsClient->getApiUrl() . '/piwik.php?idsite=' . $this->jsClient->getSiteId();
    }

    /**
     * @return boolean
     */
    public function getEnableLinkTracking()
    {
        return $this->jsClient->getEnableLinkTracking();
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        if (!$this->jsClient->getQueryString()) {
            $this->jsClient->doTrackPageView('');
        }
        return $this->jsClient->getQueryString();
    }

    /**
     * @return boolean
     */
    public function jsEnabled()
    {
        return $this->jsEnabled;
    }
} 
