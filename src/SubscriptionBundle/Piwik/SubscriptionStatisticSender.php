<?php

namespace SubscriptionBundle\Piwik;

use CountryCarrierDetectionBundle\Service\MaxMindIpInfo;
use IdentificationBundle\Entity\User;
use PiwikBundle\Service\DTO\PiwikDTO;
use PiwikBundle\Service\PiwikDataMapper;
use PiwikBundle\Service\PiwikTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\DataMapper\PiwikSubscriptionDataMapper;
use SubscriptionBundle\Piwik\DataMapper\PiwikUnsubscriptionDataMapper;

class SubscriptionStatisticSender
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PiwikTracker
     */
    private $piwikTracker;
    /**
     * @var PiwikDataMapper
     */
    private $piwikDataMapper;
    /**
     * @var PiwikSubscriptionDataMapper
     */
    private $piwikSubscriptionDataMapper;
    /**
     * @var MaxMindIpInfo
     */
    private $maxMindIpInfo;
    /**
     * @var PiwikUnsubscriptionDataMapper
     */
    private $piwikUnsubscriptionDataMapper;

    /**
     * SubscriptionStatisticSender constructor.
     *
     * @param LoggerInterface               $logger
     * @param PiwikTracker                  $newTracker
     * @param PiwikDataMapper               $piwikDataMapper
     * @param PiwikSubscriptionDataMapper   $piwikSubscriptionDataMapper
     * @param MaxMindIpInfo                 $maxMindIpInfo
     * @param PiwikUnsubscriptionDataMapper $piwikUnsubscriptionDataMapper
     */
    public function __construct(LoggerInterface $logger,
        PiwikTracker $newTracker,
        PiwikDataMapper $piwikDataMapper,
        PiwikSubscriptionDataMapper $piwikSubscriptionDataMapper,
        MaxMindIpInfo $maxMindIpInfo,
        PiwikUnsubscriptionDataMapper $piwikUnsubscriptionDataMapper)
    {
        $this->logger                        = $logger;
        $this->piwikTracker                  = $newTracker;
        $this->piwikDataMapper               = $piwikDataMapper;
        $this->piwikSubscriptionDataMapper   = $piwikSubscriptionDataMapper;
        $this->maxMindIpInfo                 = $maxMindIpInfo;
        $this->piwikUnsubscriptionDataMapper = $piwikUnsubscriptionDataMapper;
    }

    /**
     * @param User          $user
     * @param Subscription  $subscriptionEntity
     * @param ProcessResult $responseData
     * @param bool|null     $conversionMode
     *
     * @return bool
     */
    public function trackSubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackSubscribe'
            ]);

            $ecommerceDTO = $this->piwikSubscriptionDataMapper->getEcommerceDTO($user,
                $subscriptionEntity,
                $responseData,
                'subscribe');

            $additionData = $this->piwikSubscriptionDataMapper->getAdditionalData($user, $responseData->getProvider(), $conversionMode);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikSubscriptionDataMapper->getAffiliateString($subscriptionEntity)
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData($additionData, true);

            $result = $this->piwikTracker->sendEcommerce($ecommerceDTO);

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage(), 'line' => $ex->getLine(), 'code' => $ex->getCode()]);

            return false;
        }
    }

    /**
     * @param User          $user
     * @param Subscription  $subscriptionEntity
     * @param ProcessResult $responseData
     * @param bool|null     $conversionMode
     *
     * @return bool
     */
    public function trackResubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackResubscribe'
            ]);

            $ecommerceDTO = $this->piwikSubscriptionDataMapper->getEcommerceDTO($user,
                $subscriptionEntity,
                $responseData,
                'resubscribe');

            $additionData = $this->piwikSubscriptionDataMapper->getAdditionalData($user, $responseData->getProvider(), $conversionMode);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikSubscriptionDataMapper->getAffiliateString($subscriptionEntity)
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData($additionData, true);

            $result = $this->piwikTracker->sendEcommerce($ecommerceDTO);

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage(), 'line' => $ex->getLine(), 'code' => $ex->getCode()]);

            return false;
        }
    }

    /**
     * @param User          $user
     * @param Subscription  $subscriptionEntity
     * @param ProcessResult $responseData
     * @param bool|null     $conversionMode
     *
     * @return bool
     */
    public function trackRenew(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackRenew'
            ]);

            $ecommerceDTO = $this->piwikSubscriptionDataMapper->getEcommerceDTO($user,
                $subscriptionEntity,
                $responseData,
                'renew');

            $additionData = $this->piwikSubscriptionDataMapper->getAdditionalData($user, $responseData->getProvider(), $conversionMode);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikSubscriptionDataMapper->getAffiliateString($subscriptionEntity)
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData($additionData, true);

            $result = $this->piwikTracker->sendEcommerce($ecommerceDTO);

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage(), 'line' => $ex->getLine(), 'code' => $ex->getCode()]);

            return false;
        }
    }

    /**
     * @param User          $user
     * @param Subscription  $subscriptionEntity
     * @param ProcessResult $responseData
     * @param string|null   $conversionMode
     *
     * @return bool
     */
    public function trackUnsubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        string $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackUnsubscribe'
            ]);

            $ecommerceDTO = $this->piwikUnsubscriptionDataMapper->getEcommerceDTO($user,
                $subscriptionEntity,
                $responseData,
                'unsubscribe');

            $additionData = $this->piwikUnsubscriptionDataMapper->getAdditionalData($user, $responseData->getProvider(), $conversionMode);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikUnsubscriptionDataMapper->getAffiliateString($subscriptionEntity)
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData($additionData, true);

            $result = $this->piwikTracker->sendEcommerce($ecommerceDTO);

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage(), 'line' => $ex->getLine(), 'code' => $ex->getCode()]);

            return false;
        }
    }

    /**
     * @param string        $trackEventName
     * @param User          $user
     * @param Subscription  $subscriptionEntity
     * @param ProcessResult $responseData
     * @param bool|null     $conversionMode
     *
     * @return bool
     */
    public function send(
        string $trackEventName,
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName'      => $trackEventName,
                'conversionMode' => $conversionMode
            ]);

            $result = $this->$trackEventName(
                $user,
                $subscriptionEntity,
                $responseData,
                $conversionMode
            );

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage(), 'line' => $ex->getLine(), 'code' => $ex->getCode()]);

            return false;
        }
    }
}