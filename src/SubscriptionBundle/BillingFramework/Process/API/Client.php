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

    /** @var  boolean */
    private $fakeModeEnabled;
    /** @var  string */
    private $fakeUserIdentifier;
    /** @var  string */
    private $fakeUserIp;
    /**
     * @var ProcessResponseMapper
     */
    private $responseMapper;

    /**
     * BillingFrameworkAPI constructor.
     * @param EventDispatcherInterface              $eventDispatcher
     * @param ClientInterface                       $httpClient
     * @param LinkCreator                           $billingFrameworkLinkCreator
     * @param AdapterInterface                      $cache
     * @param ProcessResponseMapper                 $responseMapper
     * @param                                       $fakeModeEnabled
     * @param                                       $fakeUserIdentifier
     * @param                                       $fakeUserIp
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ClientInterface $httpClient,
        LinkCreator $billingFrameworkLinkCreator,
        AdapterInterface $cache,
        ProcessResponseMapper $responseMapper,
        $fakeModeEnabled,
        $fakeUserIdentifier,
        $fakeUserIp
    )
    {
        $this->eventDispatcher             = $eventDispatcher;
        $this->httpClient                  = $httpClient;
        $this->billingFrameworkLinkCreator = $billingFrameworkLinkCreator;
        $this->cache                       = $cache;
        $this->fakeModeEnabled             = $fakeModeEnabled;
        $this->fakeUserIdentifier          = $fakeUserIdentifier;
        $this->fakeUserIp                  = $fakeUserIp;
        $this->responseMapper              = $responseMapper;
    }


    /**
     * @param              $options
     * @param              $method
     * @return \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     * @throws EmptyResponse
     */
    public function sendPostProcessRequest(array $options, $method)
    {

        $url      = $this->billingFrameworkLinkCreator->createProcessLink($method);
        $response = $this->makePostRequest($url, $options);
        $type     = !empty($response) && isset($response->data) && isset($response->data->type)
            ? $response->data->type
            : $method;

        $processedResponse = $this->responseMapper->map($type, $response);

        return $processedResponse;
    }

    /**
     * @param ResponseInterface $response
     * @return null | stdClass | stdClass[]
     */
    protected function extractContentFromResponse(ResponseInterface $response): stdClass
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

    /**
     * @param $method
     * @return stdClass[]
     * @throws BillingFrameworkException
     * @throws BillingFrameworkProcessException
     */
    public function sendGetDataRequest(string $method): stdClass
    {
        $url = $this->billingFrameworkLinkCreator->createDataLink($method);

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


    private function handleForFakeRequestIfNeeded($options)
    {
        if ($this->fakeModeEnabled) {
            $options['fake'] = [
                'code' => 'OK',
                'data' => [
                    "id"              => "805",
                    "subtype"         => "final",
                    'status'          => 'successful',
                    "provider"        => "10",
                    "provider_id"     => 10519045527,
                    "charge_value"    => 0,
                    "charge_currency" => "EGP",
                    "charge_product"  => "16",
                    "charge_tier"     => "2",
                    "charge_strategy" => "1"
                ]
            ];
        }

        $returnFields = ['charge_product', 'charge_tier', 'charge_strategy', 'carrier', 'client_id', 'client_user'];
        foreach ($returnFields as $key) {
            if (isset($options[$key])) {
                $options['fake']['data'][$key] = $options[$key];
            }
        }
        return $options;
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
    private function makePostRequest($url, array $params)
    {
        try {

            $params           = $this->handleForFakeRequestIfNeeded($params);
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
    protected function makeBillingResponseException(ClientException $e): BillingFrameworkProcessException
    {
        $content          = $this->extractContentFromResponse($e->getResponse());
        $processException = new BillingFrameworkProcessException(null, $e->getCode(), $e);
        $processException->setRawResponse($content);
        try {
            $processException->setResponse($this->responseMapper->map('', $content));
        } catch (EmptyResponse $exception) {
        }
        return $processException;
    }
}