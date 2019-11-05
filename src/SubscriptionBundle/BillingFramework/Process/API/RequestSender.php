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

//            $preparedParams    = $this->extractor->extractParameters($processParameters);
//            $response          = $this->apiClient->sendPostProcessRequest($preparedParams, $type);


            $response = (object)json_decode('
            {"environment":{"duration":0.94746,"reverse_key":null,"status":{"id":44,"code":"SUBSCRIBE_OK","group":"success","http_code":"200","success":true},"time":1572825681.1746,"duration_crawling":0.91523814201355},"data":{"id":"29020044","client":"vod-store","client_id":"1bfce6de-090f-457d-9962-0e10e7d087f6","client_user":"923013324837","client_fields":{"listener_wait":"http:\/\/100sport.tv\/v2\/callback\/listen","redirect_url":"http:\/\/100sport.tv","user_ip":"119.160.119.95","user_headers":"host: 100sport.tv\r\naccept: text\/html,application\/xhtml+xml,application\/xml;q=0.9,image\/webp,*\/*;q=0.8\r\naccept-encoding: gzip,deflate\r\naccept-language: en-US\r\ncache-control: no-cache\r\npragma: no-cache\r\nreferer: http:\/\/100sport.tv\/identification\/pixel\/show-page?pixelUrl=http%3A\/\/www.dot-jo.biz\/appgw\/DOT.gif%3FtransactionId%3D29019994%26partnerId%3D100sport-cdd4a384&carrier=338&processId=29019994&signature=55e5db4a303a21093429d9dbfc3354b3\r\nuser-agent: Mozilla\/5.0 (Linux; Android 4.4.2; Hol-U19 Build\/HUAWEIHol-U19) AppleWebKit\/537.36 (KHTML, like Gecko) Version\/4.0 Chrome\/30.0.0.0 Mobile Safari\/537.36\r\nx-requested-with: com.app2game.romantic.photo.frames\r\nx-wap-profile: http:\/\/218.249.47.94\/Xianghe\/MTK_Phone_KK_UAprofile.xml\r\nx-forwarded-for: 119.160.119.95\r\nx-forwarded-port: 80\r\nx-forwarded-proto: http\r\nconnection: keep-alive\r\nx-php-ob-level: 1\r\n"},"type":"subscribe","subtype":"final","status":"successful","carrier":"338","provider":"111","charge_full":false,"charge_value":"11.9500","charge_paid":"0","charge_currency":"PKR","charge_product":"1bfce6de-090f-457d-9962-0e10e7d087f6","charge_tier":"2","charge_strategy":"11","insertdate":"2019-11-04 00:01:20.242100","listeners":[{"id":"547348814","link":"http:\/\/100sport.tv\/v2\/callback\/listen","client":"vod-store","process":"29020044","config":{"fields_changed":["status"],"subtypes":["redirect","pixel","direct","final","provider"],"statuses":["successful","failed"]},"bool_undeletable":false,"insertdate":"2019-11-04 00:01:20.247400"}]}}
            ');

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