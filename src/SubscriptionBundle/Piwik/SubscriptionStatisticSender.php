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
     * @var ProcessResultVerifier
     */
    private $resultVerifier;

    /**
     * SubscriptionStatisticSender constructor.
     *
     * @param LoggerInterface               $logger
     * @param PiwikTracker                  $piwikTracker
     * @param PiwikDataMapper               $piwikDataMapper
     * @param PiwikSubscriptionDataMapper   $piwikSubscriptionDataMapper
     * @param MaxMindIpInfo                 $maxMindIpInfo
     * @param PiwikUnsubscriptionDataMapper $piwikUnsubscriptionDataMapper
     * @param ProcessResultVerifier         $resultVerifier
     */
    public function __construct(LoggerInterface $logger,
        PiwikTracker $piwikTracker,
        PiwikDataMapper $piwikDataMapper,
        PiwikSubscriptionDataMapper $piwikSubscriptionDataMapper,
        MaxMindIpInfo $maxMindIpInfo,
        PiwikUnsubscriptionDataMapper $piwikUnsubscriptionDataMapper,
        ProcessResultVerifier $resultVerifier)
    {
        $this->logger                        = $logger;
        $this->piwikTracker                  = $piwikTracker;
        $this->piwikDataMapper               = $piwikDataMapper;
        $this->piwikSubscriptionDataMapper   = $piwikSubscriptionDataMapper;
        $this->maxMindIpInfo                 = $maxMindIpInfo;
        $this->piwikUnsubscriptionDataMapper = $piwikUnsubscriptionDataMapper;
        $this->resultVerifier                = $resultVerifier;
    }

    /**
     * @param User          $user
     * @param Subscription  $subscription
     * @param ProcessResult $responseData
     * @param bool|null     $conversionMode
     *
     * @return bool
     */
    public function trackSubscribe(
        User $user,
        Subscription $subscription,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackSubscribe'
            ]);

            if ($this->resultVerifier->cantTrackSubscription($responseData)) {
                return false;
            }

            $resultStatus = $this->resultVerifier->isSuccessSubscribe($responseData);
            $ecommerceDTO = $this->piwikSubscriptionDataMapper->getEcommerceDTO($subscription, $responseData, 'subscribe', $resultStatus);

            $additionData = $this->piwikSubscriptionDataMapper->getAdditionalData($subscription, $responseData->getProvider(), $conversionMode);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikSubscriptionDataMapper->getAffiliateString($subscription)
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData($additionData);

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
     * @param Subscription  $subscription
     * @param ProcessResult $responseData
     * @param bool|null     $conversionMode
     *
     * @return bool
     */
    public function trackResubscribe(
        User $user,
        Subscription $subscription,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackResubscribe'
            ]);

            if ($this->resultVerifier->cantTrackSubscription($responseData)) {
                return false;
            }
            $resultStatus = $this->resultVerifier->isSuccessSubscribe($responseData);
            $ecommerceDTO = $this->piwikSubscriptionDataMapper->getEcommerceDTO($subscription, $responseData, 'resubscribe', $resultStatus);

            $additionData = $this->piwikSubscriptionDataMapper->getAdditionalData($subscription, $responseData->getProvider(), $conversionMode);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikSubscriptionDataMapper->getAffiliateString($subscription)
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData($additionData);

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
     * @param Subscription  $subscription
     * @param ProcessResult $responseData
     * @param bool|null     $conversionMode
     *
     * @return bool
     */
    public function trackRenew(
        User $user,
        Subscription $subscription,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackRenew'
            ]);

            if ($this->resultVerifier->cantTrackSubscription($responseData)) {
                return false;
            }
            $resultStatus = $this->resultVerifier->isSuccessSubscribe($responseData);
            $ecommerceDTO = $this->piwikSubscriptionDataMapper->getEcommerceDTO($subscription, $responseData, 'renew', $resultStatus);

            $additionData = $this->piwikSubscriptionDataMapper->getAdditionalData($subscription, $responseData->getProvider(), $conversionMode);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikSubscriptionDataMapper->getAffiliateString($subscription)
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData($additionData);

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
     * @param Subscription  $subscription
     * @param ProcessResult $responseData
     * @param string|null   $conversionMode
     *
     * @return bool
     */
    public function trackUnsubscribe(
        User $user,
        Subscription $subscription,
        ProcessResult $responseData,
        string $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackUnsubscribe'
            ]);

            if ($this->resultVerifier->cantTrackUnsubscription($responseData)) {
                return false;
            }
            $resultStatus = $this->resultVerifier->isSuccessUnsubscribe($responseData);
            $ecommerceDTO = $this->piwikUnsubscriptionDataMapper->getEcommerceDTO($subscription, $responseData, 'unsubscribe', $resultStatus);

            $additionData = $this->piwikUnsubscriptionDataMapper->getAdditionalData($subscription, $responseData->getProvider(), $conversionMode);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikUnsubscriptionDataMapper->getAffiliateString($subscription)
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData($additionData);

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