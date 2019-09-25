<?php

namespace App\Piwik;

use App\Domain\Entity\Game;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Repository\CarrierRepository;
use App\Piwik\DataMapper\GameEventMapper;
use App\Piwik\DataMapper\VideoEventMapper;
use App\Piwik\DataMapper\VisitEventMapper;
use CountryCarrierDetectionBundle\Service\IpService;
use CountryCarrierDetectionBundle\Service\MaxMindIpInfo;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use App\Piwik\DTO\VisitDTO;
use PiwikBundle\Service\PiwikDataMapper;
use PiwikBundle\Service\PiwikTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\EventPublisher;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ContentStatisticSender
 */
class ContentStatisticSender
{

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
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var IpService
     */
    private $ipService;
    /**
     * @var GameEventMapper
     */
    private $GameEventMapper;
    /**
     * @var VideoEventMapper
     */
    private $VideoEventMapper;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;
    /**
     * @var EventPublisher
     */
    private $eventPublisher;
    /**
     * @var VisitEventMapper
     */
    private $visitEventMapper;

    /**
     * ContentStatisticSender constructor
     *
     * @param UserRepository     $userRepository
     * @param LoggerInterface    $logger
     * @param MaxMindIpInfo      $maxMindIpInfo
     * @param CarrierRepository  $carrierRepository
     * @param CampaignRepository $campaignRepository
     * @param IpService          $ipService
     * @param GameEventMapper    $GameEventMapper
     * @param VideoEventMapper   $VideoEventMapper
     * @param EventPublisher     $eventPublisher
     * @param VisitEventMapper   $visitEventMapper
     */
    public function __construct(
        UserRepository $userRepository,
        LoggerInterface $logger,
        MaxMindIpInfo $maxMindIpInfo,
        CarrierRepository $carrierRepository,
        CampaignRepository $campaignRepository,
        IpService $ipService,
        GameEventMapper $GameEventMapper,
        VideoEventMapper $VideoEventMapper,
        EventPublisher $eventPublisher,
        VisitEventMapper $visitEventMapper
    )
    {
        $this->userRepository     = $userRepository;
        $this->logger             = $logger;
        $this->maxMindIpInfo      = $maxMindIpInfo;
        $this->carrierRepository  = $carrierRepository;
        $this->ipService          = $ipService;
        $this->GameEventMapper    = $GameEventMapper;
        $this->VideoEventMapper   = $VideoEventMapper;
        $this->campaignRepository = $campaignRepository;
        $this->eventPublisher     = $eventPublisher;
        $this->visitEventMapper   = $visitEventMapper;
    }

    /**
     * @param SessionInterface $session
     * @return bool
     */
    public function trackVisit(SessionInterface $session): bool
    {
        $billingCarrierId    = IdentificationFlowDataExtractor::extractBillingCarrierId($session);
        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($session);
        $campaignToken       = AffiliateVisitSaver::extractCampaignToken($session);

        try {
            /** @var User $user */
            $user        = $this->userRepository->findOneBy(['identificationToken' => $identificationToken]);
            $userIp      = $user->getIp();
            $countryCode = $user->getCountry();
            $msisdn      = $user->getIdentifier();
        } catch (\Throwable $e) {
            $carrier     = $billingCarrierId
                ? $this->carrierRepository->findOneByBillingId($billingCarrierId)
                : null;
            $countryCode = $carrier
                ? $carrier->getCountryCode()
                : null;
            $user        = null;
            $msisdn      = null;
            $userIp      = $this->ipService->getIp();
        }

        try {
            $campaign        = $this->campaignRepository->findOneByCampaignToken($campaignToken);
            $affiliate       = $campaign->getAffiliate();
            $affiliateString = $affiliate->getUuid() . '@' . $campaign->getUuid();
        } catch (\Throwable $e) {
            $affiliateString = null;
        }

        $this->logger->info('Trying to send piwik event', [
            'eventName' => 'pageVisit'
        ]);


        $visitDTO = new VisitDTO(
            $countryCode,
            $userIp,
            $this->maxMindIpInfo->getConnectionType(),
            $msisdn,
            $billingCarrierId,
            $affiliateString
        );

        $event  = $this->visitEventMapper->map($visitDTO);
        $result = $this->eventPublisher->publish($event);

        return $result;
    }

    /**
     * @param Subscription $subscription
     * @param Game         $game
     *
     * @return bool
     */
    public function trackDownload(Subscription $subscription, Game $game): bool
    {
        $this->logger->info('Trying to send piwik event', [
            'eventName' => 'trackDownload'
        ]);

        $event  = $this->GameEventMapper->map($subscription, $game);
        $result = $this->eventPublisher->publish($event);

        return $result;
    }

    /**
     * @param UploadedVideo $uploadedVideo
     * @param Subscription  $subscription
     *
     * @return bool
     */
    public function trackPlayingVideo(UploadedVideo $uploadedVideo, Subscription $subscription): bool
    {
        $this->logger->info('Trying to send piwik event', [
            'eventName' => 'trackPlayingVideo'
        ]);

        $event  = $this->VideoEventMapper->map($subscription, $uploadedVideo);
        $result = $this->eventPublisher->publish($event);

        return $result;
    }
}