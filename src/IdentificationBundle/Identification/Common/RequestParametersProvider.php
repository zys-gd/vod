<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.05.18
 * Time: 10:25
 */

namespace IdentificationBundle\Identification\Common;


use SubscriptionBundle\BillingFramework\BillingOptionsProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Subscription\Common\RouteProvider;

class RequestParametersProvider
{
    /**
     * @var \SubscriptionBundle\Subscription\Common\RouteProvider
     */
    private $routeProvider;
    /**
     * @var BillingOptionsProvider
     */
    private $billingOptionsProvider;


    /**
     * @param \SubscriptionBundle\Subscription\Common\RouteProvider $routeProvider
     * @param BillingOptionsProvider                                $billingOptionsProvider
     */
    public function __construct(
        RouteProvider $routeProvider,
        BillingOptionsProvider $billingOptionsProvider
    )
    {

        $this->routeProvider          = $routeProvider;
        $this->billingOptionsProvider = $billingOptionsProvider;
    }

    /**
     * @param string $identificationToken
     * @param int    $carrierId
     * @param string $clientIp
     * @param string $redirectUrl
     * @param array  $headers
     * @param array  $additionalData
     * @return ProcessRequestParameters
     */
    public function prepareRequestParameters(
        string $identificationToken,
        int $carrierId,
        string $clientIp,
        string $redirectUrl,
        array $headers = [],
        array $additionalData = []
    ): ProcessRequestParameters
    {


        $parameters               = new ProcessRequestParameters();
        $parameters->listener     = $this->routeProvider->getAbsoluteLinkForCallback('identify_callback');
        $parameters->client       = $this->billingOptionsProvider->getClientId();
        $parameters->listenerWait = $this->routeProvider->getAbsoluteLinkForCallback('identify_callback');
        $parameters->clientId     = $identificationToken;
        $parameters->carrier      = $carrierId;
        $parameters->userIp       = $clientIp;
        $parameters->redirectUrl  = $redirectUrl;

        // The request headers of the end user.
        $currentUserRequestHeaders = '';
        foreach ($headers as $key => $value) {
            $currentUserRequestHeaders .= "{$key}: {$value[0]}\r\n";
        }
        $parameters->userHeaders    = $currentUserRequestHeaders;
        $parameters->additionalData = $additionalData;

        return $parameters;
    }
}