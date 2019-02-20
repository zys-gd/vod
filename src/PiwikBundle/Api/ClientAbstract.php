<?php

namespace PiwikBundle\Api;

use PiwikBundle\Service\RabbitMQProducer;
use PiwikTracker;

/**
 * Class ClientAbstract
 */
abstract class ClientAbstract extends PiwikTracker
{
    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var RabbitMQProducer
     */
    protected $rabbitMQProducer;

    const EXCHANGE_NAME = 'piwik-events-2';
    const ROUTING_KEY = 'piwik-events-route';
    const QUEUE_NAME = 'piwik-events-send';

    /**
     * PhpClient constructor.
     *
     * @param int     $idSite
     * @param string  $host
     * @param boolean $https
     * @param string  $tokenAuth
     */
    public function __construct($idSite, $host, $https, $tokenAuth)
    {
        $this->apiUrl = ($https ? 'https' : 'http') . '://' . $host;

        parent::__construct($idSite, $this->apiUrl);

        $this->token_auth = $tokenAuth;

    }

    /**
     * @param RabbitMQProducer $rabbitMQProducer
     */
    public function setRabbitMQProducer(RabbitMQProducer $rabbitMQProducer)
    {
        $this->rabbitMQProducer = $rabbitMQProducer;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
        return $this->idSite;
    }

    /**
     * @inheritdoc
     */
    protected function sendRequest($url, $method = 'GET', $data = null, $force = false)
    {
        self::$DEBUG_LAST_REQUESTED_URL = $url;

        // if doing a bulk request, store the url
        if ($this->doBulkRequests && !$force) {
            $this->storedTrackingActions[]
                = $url
                . (!empty($this->userAgent) ? ('&ua=' . urlencode($this->userAgent)) : '')
                . (!empty($this->acceptLanguage) ? ('&lang=' . urlencode($this->acceptLanguage)) : '');

            // Clear custom variables so they don't get copied over to other users in the bulk request
            $this->clearCustomVariables();
            $this->clearCustomTrackingParameters();
            $this->userAgent      = false;
            $this->acceptLanguage = false;

            return true;
        }

        if (function_exists('curl_init') && function_exists('curl_exec')) {
            $options = array(
                CURLOPT_URL            => $url,
                CURLOPT_USERAGENT      => $this->userAgent,
                CURLOPT_HEADER         => true,
                CURLOPT_TIMEOUT        => $this->requestTimeout,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => array(
                    'Accept-Language: ' . $this->acceptLanguage,
                ),
            );

            if (defined('PATH_TO_CERTIFICATES_FILE')) {
                $options[CURLOPT_CAINFO] = PATH_TO_CERTIFICATES_FILE;
            }

            switch ($method) {
                case 'POST':
                    $options[CURLOPT_POST] = true;
                    break;
                default:
                    break;
            }

            // only supports JSON data
            if (!empty($data)) {
                $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                $options[CURLOPT_HTTPHEADER][] = 'Expect:';
                $options[CURLOPT_POSTFIELDS]   = $data;
            }

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            ob_start();
            $response = @curl_exec($ch);
            ob_end_clean();
            $content = '';
            if (!empty($response)) {
                list($header, $content) = explode("\r\n\r\n", $response, $limitCount = 2);
            }
        } else {
            if (function_exists('stream_context_create')) {
                $stream_options = array(
                    'http' => array(
                        'method'     => $method,
                        'user_agent' => $this->userAgent,
                        'header'     => "Accept-Language: " . $this->acceptLanguage . "\r\n",
                        'timeout'    => $this->requestTimeout, // PHP 5.2.1
                    ),
                );

                // only supports JSON data
                if (!empty($data)) {
                    $stream_options['http']['header']  .= "Content-Type: application/json \r\n";
                    $stream_options['http']['content'] = $data;
                }
                $ctx      = stream_context_create($stream_options);
                $response = file_get_contents($url, 0, $ctx);
                $content  = $response;
            }
        }

        return $content;
    }

    /*OPTI */
    protected function getRequest($idSite)
    {
        $this->urlReferrer = substr($this->urlReferrer, 0, 1000);
        return parent::getRequest($idSite);
    }
}