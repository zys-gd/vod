<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.05.18
 * Time: 12:44
 */

namespace SubscriptionBundle\BillingFramework\Process\API;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;
use SubscriptionBundle\BillingFramework\Process\API\Exception\EmptyResponse;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Client
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $httpClient;
    /**
     * @var LinkCreator
     */
    private $billingFrameworkLinkCreator;
    /**
     * @var AdapterInterface
     */
    private $cache;
    /**
     * @var ProcessResponseMapper
     */
    private $responseMapper;

    /**
     * BillingFrameworkAPI constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param ClientInterface          $httpClient
     * @param LinkCreator              $billingFrameworkLinkCreator
     * @param AdapterInterface         $cache
     * @param ProcessResponseMapper    $responseMapper
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ClientInterface $httpClient,
        LinkCreator $billingFrameworkLinkCreator,
        AdapterInterface $cache,
        ProcessResponseMapper $responseMapper
    )
    {
        $this->eventDispatcher             = $eventDispatcher;
        $this->httpClient                  = $httpClient;
        $this->billingFrameworkLinkCreator = $billingFrameworkLinkCreator;
        $this->cache                       = $cache;
        $this->responseMapper              = $responseMapper;
    }

    /**
     * @param array        $options
     * @param              $method
     *
     * @return null|stdClass|stdClass[]
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     */
    public function sendPostProcessRequest(array $options, $method): ?stdClass
    {
        $url      = $this->billingFrameworkLinkCreator->createProcessLink($method);
        $response = $this->makePostRequest($url, $options);
        return $response;
    }

    /**
     * @param array $options
     * @param       $method
     *
     * @return ResponseInterface
     * @throws BillingFrameworkException
     * @throws GuzzleException
     */
    public function sendPostProcessRequestWithoutExtraction(array $options, $method): ResponseInterface
    {
        $url      = $this->billingFrameworkLinkCreator->createProcessLink($method);
        $response = $this->makePostRequestWithoutExtraction($url, $options);
        return $response;
    }

    /**
     * @param string $method
     * @param array  $options
     *
     * @return stdClass|stdClass[]|null
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     */
    public function sendGetRequest(string $method, array $options = [])
    {
        $url = $this->billingFrameworkLinkCreator->createProcessLink($method, $options);
        return $this->performGetRequest($url);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return null | stdClass | stdClass[]
     */
    private function extractContentFromResponse(ResponseInterface $response): ?stdClass
    {

        $data = null;
        $body = $response->getBody();
        if ($body instanceof StreamInterface) {
            $contents = $body->getContents();
            if ($contents) {
                /** @var stdClass $parsedResponse */
                $data = json_decode($contents);
            }
        }
        return $data;
    }

    public function sendGetDataRequestWithoutCache(string $method, string $id = null): stdClass
    {
        $url      = $this->billingFrameworkLinkCreator->createDataLink($method, $id);
        $response = $this->performGetRequest($url);
        return (object)$response->data;
    }

    /**
     * @param string      $method
     * @param string|null $id
     *
     * @return stdClass
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     */
    public function sendGetDataRequest(string $method, string $id = null): stdClass
    {
        $url = $this->billingFrameworkLinkCreator->createDataLink($method, $id);

        try {
            $cachedResponse = $this->cache->getItem($this->generateCacheKey($method));
        } catch (\Psr\Cache\InvalidArgumentException $ex) {
            return $this->performGetRequest($url);
        }

        if (!$cachedResponse->isHit()) {
            $response = $this->performGetRequest($url);
            $cachedResponse->set($response);
            $this->cache->save($cachedResponse);
        } else {
            $response = $cachedResponse->get();
        }

        $resultData = $response && property_exists($response, 'data') ? $response->data : [];

        return (object)$resultData;

    }


    private function generateCacheKey($method)
    {
        return str_replace("/", "-", $method);
    }

    /**
     * @param $url
     *
     * @return null|stdClass|stdClass[]
     * @throws BillingFrameworkProcessException
     * @throws BillingFrameworkException
     */
    private function performGetRequest($url)
    {
        try {
            $response         = $this->httpClient->request('GET', $url);
            $preparedResponse = $this->extractContentFromResponse($response);
            return $preparedResponse;
        } catch (RequestException $e) {
            throw $this->convertRequestException($e);
        } catch (\Exception $e) {
            throw $this->convertException($e);
        }


    }

    /**
     * @param       $url
     * @param array $params
     * @param bool  $isJson
     *
     * @return null|stdClass|stdClass[]
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     * @throws GuzzleException
     */
    public function makePostRequest($url, array $params, bool $isJson = false): ?stdClass
    {
        try {

            if ($isJson) {
                $options = [
                    RequestOptions::JSON => $params,
                ];
            } else {
                $options = [
                    RequestOptions::FORM_PARAMS => $params,
                ];
            }
            $response         = $this->httpClient->request('POST', $url, $options);
            $preparedResponse = $this->extractContentFromResponse($response);
            return $preparedResponse;
        } catch (RequestException $e) {
            throw $this->convertRequestException($e);
        } catch (\Exception $e) {
            throw $this->convertException($e);
        }
    }

    /**
     * @param string $url
     * @param array  $params
     * @param bool   $isJson
     *
     * @return ResponseInterface
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     * @throws GuzzleException
     */
    private function makePostRequestWithoutExtraction(
        string $url,
        array $params = [],
        bool $isJson = false): ResponseInterface
    {
        $options = $isJson
            ? [RequestOptions::JSON => $params]
            : [RequestOptions::FORM_PARAMS => $params];

        try {
            return $this->httpClient->request('POST', $url, $options);
        } catch (RequestException $e) {
            throw $this->convertRequestException($e);
        } catch (\Exception $e) {
            throw $this->convertException($e);
        }
    }

    private function convertRequestException(RequestException $e): BillingFrameworkProcessException
    {
        $content          = $this->extractContentFromResponse($e->getResponse());
        $processException = new BillingFrameworkProcessException(
            sprintf('%s: %s', get_class($e), $e->getMessage()),
            $e->getCode(),
            $e
        );
        $processException->setRawResponse($content);
        try {
            $processException->setResponse($this->responseMapper->map('', $content));

            if (isset($content->data) && isset($content->data->code)) {
                $processException->setBillingCode($content->data->code);
            }
        } catch (EmptyResponse $exception) {
        }
        return $processException;
    }

    private function convertException(\Exception $e): BillingFrameworkException
    {
        return new BillingFrameworkException(
            sprintf('%s: %s', get_class($e), $e->getMessage()),
            $e->getCode(),
            $e
        );
    }
}