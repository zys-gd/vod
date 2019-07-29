<?php


namespace IdentificationBundle\Identification\Common;


use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\BillingOptionsProvider;
use SubscriptionBundle\BillingFramework\Process\API\Client;
use SubscriptionBundle\BillingFramework\Process\API\LinkCreator;

class PostPaidHandler
{

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var LinkCreator
     */
    private $linkCreator;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var BillingOptionsProvider
     */
    private $billingOptionsProvider;

    public function __construct(
        LoggerInterface $logger,
        Client $client,
        LinkCreator $linkCreator,
        IdentificationDataStorage $identificationDataStorage,
        BillingOptionsProvider $billingOptionsProvider
    )
    {
        $this->logger                    = $logger;
        $this->client                    = $client;
        $this->linkCreator               = $linkCreator;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->billingOptionsProvider    = $billingOptionsProvider;
    }

    /**
     * @param string $msisdn
     * @param int    $billingCarrierId
     */
    public function process(string $msisdn, int $billingCarrierId): void
    {
        $this->logger->debug('Start checking for postpaid status', [
            'msisdn'           => $msisdn,
            'billingCarrierId' => $billingCarrierId
        ]);

        $preparedParams = [
            'msisdn'     => $msisdn,
            'client'     => $this->billingOptionsProvider->getClientId(),
            'auth_token' => base64_encode('snooker_in_colombo_investigating_aircrash')
        ];

        $link = $this->linkCreator->createCustomLink(sprintf('public/special/%s/postpaidcheck', $billingCarrierId));
        try {
            $response = $this->client->makePostRequest($link, $preparedParams);
            $data     = (array)$response->data;
            $this->identificationDataStorage->storeValue(
                IdentificationDataStorage::POST_PAID_RESTRICTED_KEY,
                $data['accountTypeId'] ?? false
            );
        } catch (\Throwable $e) {
            $this->logger->debug('Postpaid error', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @return bool
     */
    public function isPostPaidRestricted()
    {
        return $this->identificationDataStorage->readValue(IdentificationDataStorage::POST_PAID_RESTRICTED_KEY);
    }
}