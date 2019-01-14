<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.05.18
 * Time: 12:44
 */

namespace SubscriptionBundle\BillingFramework\Process\API;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\Exception\EmptyResponse;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;

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
     * @return null|stdClass|stdClass[]
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     */
    public function sendPostRequest(array $options, $method): ?stdClass
    {
        $url      = $this->billingFrameworkLinkCreator->createProcessLink($method);
        $response = $this->makePostRequest($url, $options);
        return $response;
    }

    /**
     * @param ResponseInterface $response
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
        return (object)$response->data;

    }


    private function generateCacheKey($method)
    {
        return str_replace("/", "-", $method);
    }

    /**
     * @param $url
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
        } catch (ClientException $e) {
            throw $this->makeBillingResponseException($e);
        } catch (GuzzleException $e) {
            throw new BillingFrameworkException(null, $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new BillingFrameworkException(null, $e->getCode(), $e);
        }


    }

    /**
     * @param $url
     * @param $params
     * @return null|stdClass|stdClass[]
     * @throws BillingFrameworkProcessException
     * @throws BillingFrameworkException
     */
    private function makePostRequest($url, array $params): ?stdClass
    {
        try {

            $response         = $this->httpClient->request('POST', $url, [\GuzzleHttp\RequestOptions::FORM_PARAMS => $params]);
            $preparedResponse = $this->extractContentFromResponse($response);
            return $preparedResponse;
        } catch (ClientException $e) {
            throw $this->makeBillingResponseException($e);
        } catch (GuzzleException $e) {
            throw new BillingFrameworkException($e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new BillingFrameworkException($e->getMessage(), $e->getCode(), $e);
        }


    }

    /**
     * @param $e
     * @return BillingFrameworkProcessException
     */
    private function makeBillingResponseException(ClientException $e): BillingFrameworkProcessException
    {
        $content          = $this->extractContentFromResponse($e->getResponse());
        $processException = new BillingFrameworkProcessException(null, $e->getCode(), $e);
        $processException->setRawResponse($content);
        try {
            $processException->setResponse($this->responseMapper->map('', $content));

            if (isset($content->data) && $content->data->code) {
                $processException->setBillingCode($content->data->code);
            }
        } catch (EmptyResponse $exception) {
        }
        return $processException;
    }
}