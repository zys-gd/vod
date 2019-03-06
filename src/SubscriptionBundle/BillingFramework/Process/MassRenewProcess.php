<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.03.19
 * Time: 15:01
 */

namespace SubscriptionBundle\BillingFramework\Process;


use IdentificationBundle\Entity\CarrierInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\Client;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\BillingFramework\Process\API\LinkCreator;
use SubscriptionBundle\BillingFramework\Process\API\RequestParametersExtractor;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkProcessException;
use SubscriptionBundle\BillingFramework\Process\Exception\MassRenewingProcessException;

class MassRenewProcess
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
     * @var RequestParametersExtractor
     */
    private $requestParametersExtractor;
    /**
     * @var LinkCreator
     */
    private $linkCreator;


    /**
     * MassRenewProcess constructor.
     * @param LoggerInterface            $logger
     * @param Client                     $client
     * @param RequestParametersExtractor $requestParametersExtractor
     * @param LinkCreator                $linkCreator
     */
    public function __construct(
        LoggerInterface $logger,
        Client $client,
        RequestParametersExtractor $requestParametersExtractor,
        LinkCreator $linkCreator
    )
    {
        $this->logger                     = $logger;
        $this->client                     = $client;
        $this->requestParametersExtractor = $requestParametersExtractor;
        $this->linkCreator                = $linkCreator;
    }


    /**
     * @param ProcessRequestParameters $parameters
     * @param CarrierInterface         $carrier
     * @return \stdClass
     */
    public function doMassRenew(ProcessRequestParameters $parameters, CarrierInterface $carrier): \stdClass
    {
        try {

            $preparedParams = $this->requestParametersExtractor->extractParameters($parameters);
            $link           = $this->linkCreator->createCustomLink(sprintf('public/special/%s/massrenew', $carrier->getBillingCarrierId()));
            return $this->client->makePostRequest($link, $preparedParams, true);

        } catch (BillingFrameworkProcessException $exception) {
            $this->logger->error('Error while trying to renew', ['subscriptionId' => $parameters->clientId, 'params' => $parameters]);
            throw new MassRenewingProcessException('Error while trying to mass renew', $exception->getBillingCode(), $exception->getResponse()->getMessage());

        } catch (BillingFrameworkException $exception) {
            $this->logger->error('Error while trying to renew', ['subscriptionId' => $parameters->clientId, 'params' => $parameters]);
            throw new MassRenewingProcessException('Error while trying to mass renew', 0, $exception);
        }

    }
}