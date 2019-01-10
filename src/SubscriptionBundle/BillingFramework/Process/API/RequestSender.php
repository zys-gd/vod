<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 12:00
 */

namespace SubscriptionBundle\BillingFramework\Process\API;


use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;

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
     * RequestSender constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Client                   $apiClient
     * @param LoggerInterface          $logger
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Client $apiClient,
        LoggerInterface $logger
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->apiClient       = $apiClient;
        $this->logger          = $logger;
    }

    public function sendProcessRequest(string $type, ProcessRequestParameters $processParameters): ProcessResult
    {

        try {

            $preparedParams  = $this->parseParameters($processParameters);
            $processResponse = $this->apiClient->sendPostProcessRequest($preparedParams, $type);
            $this->logger->debug('Received response from billing', [
                'status'  => $processResponse->getStatus(),
                'subtype' => $processResponse->getSubtype(),
                'url'     => $processResponse->getUrl(),
                'error'   => $processResponse->getError()
            ]);

        } catch (BillingFrameworkProcessException $e) {
            $this->logger->debug('Bad response from BF', (array)$e->getRawResponse());
            throw $e;
        }

        return $processResponse;
    }

    private function parseParameters(ProcessRequestParameters $processParameters): array
    {
        $defaults = [
            'client'          => $processParameters->client,
            'listener'        => $processParameters->listener,
            'user_headers'    => $processParameters->userHeaders,
            'client_user'     => $processParameters->clientUser,
            'user_ip'         => $processParameters->userIp,
            'carrier'         => $processParameters->carrier,
            'client_id'       => $processParameters->clientId,
            'redirect_url'    => $processParameters->redirectUrl,
            'listener_wait'   => $processParameters->listenerWait,
            'charge_product'  => $processParameters->chargeProduct,
            'charge_tier'     => $processParameters->chargeTier,
            'charge_strategy' => $processParameters->chargeStrategy,
        ];
        return array_merge($defaults, $processParameters->additionalData);
    }
}