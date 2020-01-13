<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 16:39
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Service;


use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\OfflineConversionFeedService;
use Google\AdsApi\Common\AdsSoapClient;
use Google\AdsApi\Common\Configuration;
use Google\AdsApi\Common\OAuth2TokenBuilder;

class FeedConversionServiceProvider
{
    /**
     * @var GoogleCredentialsProvider
     */
    private $googleCredentialsProvider;


    /**
     * FeedConversionServiceProvider constructor.
     * @param GoogleCredentialsProvider $googleCredentialsProvider
     */
    public function __construct(GoogleCredentialsProvider $googleCredentialsProvider)
    {
        $this->googleCredentialsProvider = $googleCredentialsProvider;
    }

    public function buildService(): AdsSoapClient
    {

        $configuration = new Configuration([
            'OAUTH2'  => [
                "clientId"     => $this->googleCredentialsProvider->getClientId(),
                "clientSecret" => $this->googleCredentialsProvider->getClientSecret(),
                "refreshToken" => $this->googleCredentialsProvider->getRefreshToken(),
            ],
            'ADWORDS' => [
                "developerToken"   => $this->googleCredentialsProvider->getDeveloperToken(),
                "clientCustomerId" => $this->googleCredentialsProvider->getClientCustomerId(),
            ]
        ]);

        $oAuth2Credentials = (new OAuth2TokenBuilder())
            ->from($configuration)
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->from($configuration)
            ->withOAuth2Credential($oAuth2Credentials)
            ->build();

        $service = (new AdWordsServices())
            ->get($session, OfflineConversionFeedService::class);

        return $service;

    }
}