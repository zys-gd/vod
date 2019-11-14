<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 12:00
 */

namespace SubscriptionBundle\BillingFramework\Process\API;


use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RequestSender
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var Client
     */
    private $apiClient;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProcessResponseMapper
     */
    private $responseMapper;
    /**
     * @var RequestParametersExtractor
     */
    private $extractor;


    /**
     * EventPublisher constructor.
     * @param EventDispatcherInterface   $eventDispatcher
     * @param Client                     $apiClient
     * @param LoggerInterface            $logger
     * @param ProcessResponseMapper      $responseMapper
     * @param RequestParametersExtractor $extractor
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Client $apiClient,
        LoggerInterface $logger,
        ProcessResponseMapper $responseMapper,
        RequestParametersExtractor $extractor
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->apiClient       = $apiClient;
        $this->logger          = $logger;
        $this->responseMapper  = $responseMapper;
        $this->extractor       = $extractor;
    }

    /**
     * @param string                   $type
     * @param ProcessRequestParameters $processParameters
     * @return ProcessResult
     * @throws BillingFrameworkProcessException
     * @throws BillingFrameworkException
     */
    public function sendProcessRequest(string $type, ProcessRequestParameters $processParameters): ProcessResult
    {

        try {

            $preparedParams    = $this->extractor->extractParameters($processParameters);
            $response          = $this->apiClient->sendPostProcessRequest($preparedParams, $type);



            $processedResponse = $this->responseMapper->map($type, $response);
            $this->logger->debug('Received response from billing', [
                'status'  => $processedResponse->getStatus(),
                'subtype' => $processedResponse->getSubtype(),
                'url'     => $processedResponse->getUrl(),
                'error'   => $processedResponse->getError()
            ]);

        } catch (BillingFrameworkProcessException $e) {
            $this->logger->debug('Bad response from BF', (array)$e->getRawResponse());
            throw $e;
        }

        return $processedResponse;
    }

    /**
     * @param string                   $type
     * @param ProcessRequestParameters $processParameters
     *
     * @return \stdClass|\stdClass[]|null
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     */
    public function sendRequestWithoutResponseMapping(string $type, ProcessRequestParameters $processParameters)
    {
        try {

            $preparedParams = $this->extractor->extractParameters($processParameters);
            $response       = $this->apiClient->sendPostProcessRequest($preparedParams, $type);

        } catch (BillingFrameworkProcessException $e) {
            $this->logger->debug('Bad response from BF', (array)$e->getRawResponse());
            throw $e;
        }

        return $response;

    }

    /**
     * @param string                   $type
     * @param ProcessRequestParameters $processParameters
     *
     * @return ResponseInterface
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequestWithoutExtraction(string $type, ProcessRequestParameters $processParameters): ResponseInterface
    {
        try {

            $preparedParams = $this->extractor->extractParameters($processParameters);
            return $this->apiClient->sendPostProcessRequestWithoutExtraction($preparedParams, $type);

        } catch (BillingFrameworkProcessException $e) {
            $this->logger->debug('Bad response from BF', (array)$e->getRawResponse());
            throw $e;
        }
    }
}