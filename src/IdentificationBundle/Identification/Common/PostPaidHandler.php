<?php


namespace IdentificationBundle\Identification\Common;


use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use Psr\Log\LoggerInterface;
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

    public function __construct(LoggerInterface $logger,
        Client $client,
        LinkCreator $linkCreator,
        IdentificationDataStorage $identificationDataStorage)
    {
        $this->logger = $logger;
        $this->client = $client;
        $this->linkCreator = $linkCreator;
        $this->identificationDataStorage = $identificationDataStorage;
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
            'client'     => 'vod-store',
            'auth_token' => base64_encode('snooker_in_colombo_investigating_aircrash')
        ];

        $link = $this->linkCreator->createCustomLink(sprintf('public/special/%s/postpaidcheck', $billingCarrierId));
        try {
            $response = $this->client->makePostRequest($link, $preparedParams);
            $data = (array)$response->data;
            $this->identificationDataStorage->storeValue('isPostPaidRestricted', $data['accountTypeId'] ?? false);
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
        return $this->identificationDataStorage->readValue('isPostPaidRestricted') === 1;
    }
}