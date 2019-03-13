<?php

namespace PiwikBundle\Api;
use ExtrasBundle\Utils\TimestampGenerator;
use Psr\Log\LoggerInterface;

/**
 * Class PhpClient
 *
 * @package Playwing\PiwikBundle\Api
 */
class PhpClient extends ClientAbstract
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PhpClient constructor.
     * @param                 $idSite
     * @param                 $host
     * @param                 $https
     * @param                 $tokenAuth
     * @param LoggerInterface $logger
     */
    public function __construct($idSite, $host, $https, $tokenAuth, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($idSite, $host, $https, $tokenAuth);
    }

    /**
     * @param array $piwikData
     * @return mixed
     */
    public function sendRequestFromQueue(array $piwikData = [])
    {
        return call_user_func_array(['parent', 'sendRequest'], $piwikData);
    }

    /**
     * @inheritdoc
     */
    protected function sendRequest($url, $method = 'GET', $data = null, $force = false)
    {
        $this->logger->info('Sending Piwik event',func_get_args());

        $args = func_get_args();
        $args[0] .= !empty($this->userAgent) ? ('&ua=' . urlencode($this->userAgent)) : '';

        $dataForQueue = [$args[0], TimestampGenerator::generateMicrotime()];
        $this->rabbitMQProducer->sendEvent(json_encode(['piwikData' => $dataForQueue]));

        return true;
    }
}