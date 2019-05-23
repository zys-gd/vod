<?php

namespace App\Domain\Service\Piwik;

use App\Domain\Entity\Game;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Service\Piwik\DataMapper\PiwikGameDataMapper;
use App\Domain\Service\Piwik\DataMapper\PiwikVideoDataMapper;
use CountryCarrierDetectionBundle\Service\IpService;
use CountryCarrierDetectionBundle\Service\MaxMindIpInfo;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use PiwikBundle\Service\DTO\PiwikDTO;
use PiwikBundle\Service\PiwikDataMapper;
use PiwikBundle\Service\PiwikTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ContentStatisticSender
 */
class ContentStatisticSender
{
    /**
     * @var PiwikTracker
     */
    private $piwikTracker;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MaxMindIpInfo
     */
    private $maxMindIpInfo;

    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var PiwikDataMapper
     */
    private $piwikDataMapper;
    /**
     * @var IpService
     */
    private $ipService;
    /**
     * @var PiwikGameDataMapper
     */
    private $piwikGameDataMapper;
    /**
     * @var PiwikVideoDataMapper
     */
    private $piwikVideoDataMapper;

    /**
     * ContentStatisticSender constructor
     *
     * @param PiwikTracker          $piwikTracker
     * @param UserRepository        $userRepository
     * @param LoggerInterface       $logger
     * @param MaxMindIpInfo         $maxMindIpInfo
     * @param SubscriptionExtractor $subscriptionExtractor
     * @param CarrierRepository     $carrierRepository
     * @param CampaignExtractor     $campaignExtractor
     * @param PiwikDataMapper       $piwikDataMapper
     * @param IpService             $ipService
     * @param PiwikGameDataMapper   $piwikGameDataMapper
     * @param PiwikVideoDataMapper  $piwikVideoDataMapper
     */
    public function __construct(
        PiwikTracker $piwikTracker,
        UserRepository $userRepository,
        LoggerInterface $logger,
        MaxMindIpInfo $maxMindIpInfo,
        SubscriptionExtractor $subscriptionExtractor,
        CarrierRepository $carrierRepository,
        CampaignExtractor $campaignExtractor,
        PiwikDataMapper $piwikDataMapper,
        IpService $ipService,
        PiwikGameDataMapper $piwikGameDataMapper,
        PiwikVideoDataMapper $piwikVideoDataMapper
    )
    {
        $this->piwikTracker          = $piwikTracker;
        $this->userRepository        = $userRepository;
        $this->logger                = $logger;
        $this->maxMindIpInfo         = $maxMindIpInfo;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->carrierRepository     = $carrierRepository;
        $this->campaignExtractor     = $campaignExtractor;
        $this->piwikDataMapper       = $piwikDataMapper;
        $this->ipService             = $ipService;
        $this->piwikGameDataMapper   = $piwikGameDataMapper;
        $this->piwikVideoDataMapper  = $piwikVideoDataMapper;
    }

    /**
     * @param SessionInterface $session
     * @param ISPData          $data
     *
     * @return bool
     */
    public function trackVisit(SessionInterface $session, ISPData $data = null): bool
    {
        $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($session);

        $billingCarrierId = $data ? $data->getCarrierId() : null;

        try {
            $token = $identificationData['identification_token'];
            /** @var User $user */
            $user        = $this->userRepository->findOneBy(['identificationToken' => $token]);
            $userIp      = $user->getIp();
            $countryCode = $user->getCountry();
            $msisdn      = $user->getIdentifier();
        } catch (\Throwable $e) {
            $carrier     = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            $countryCode = $carrier->getCountryCode();
            $user        = null;
            $msisdn      = null;
            $userIp      = $this->ipService->getIp();
        }

        try {
            $campaign        = $this->campaignExtractor->getCampaignFromSession($session);
            $affiliate       = $campaign->getAffiliate();
            $affiliateString = $affiliate->getUuid() . '@' . $campaign->getUuid();
        } catch (\Throwable $e) {
            $affiliateString = null;
        }

        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'pageVisit'
            ]);

            $piwikDTO = new PiwikDTO(
                $countryCode,
                $userIp,
                $this->maxMindIpInfo->getConnectionType(),
                $msisdn,
                $billingCarrierId,
                $affiliateString
            );

            $this->piwikDataMapper->mapData($piwikDTO);
            $this->piwikDataMapper->mapAdditionalData([], true);

            $result = $this->piwikTracker->sendPageView();

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage(), 'line' => $ex->getLine(), 'code' => $ex->getCode()]);

            return false;
        }
    }

    /**
     * @param Subscription     $subscription
     * @param Game             $game
     *
     * @return bool
     */
    public function trackDownload(Subscription $subscription, Game $game): bool
    {
        $user = $subscription->getUser();

        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackDownload'
            ]);

            $ecommerceDTO = $this->piwikGameDataMapper->getEcommerceDTO($subscription, $game);

            $additionData = $this->piwikGameDataMapper->getAdditionalData($game);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikGameDataMapper->getAffiliateString($subscription)
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
     * @param UploadedVideo    $uploadedVideo
     * @param Subscription     $subscription
     *
     * @return bool
     */
    public function trackPlayingVideo(UploadedVideo $uploadedVideo, Subscription $subscription): bool
    {
        $user = $subscription->getUser();

        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'trackPlayingVideo'
            ]);

            $ecommerceDTO = $this->piwikVideoDataMapper->getEcommerceDTO($subscription, $uploadedVideo);

            $additionData = $this->piwikVideoDataMapper->getAdditionalData($uploadedVideo);

            $piwikDTO = new PiwikDTO(
                $user->getCountry(),
                $user->getIp(),
                $this->maxMindIpInfo->getConnectionType(),
                $user->getIdentifier(),
                $user->getBillingCarrierId(),
                $this->piwikVideoDataMapper->getAffiliateString($subscription)
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
     * @return string
     */
    private function getUserIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }
}