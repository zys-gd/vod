<?php

namespace App\Domain\Service;

use App\Domain\Entity\Game;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\CountryRepository;
use CountryCarrierDetectionBundle\Service\MaxMindIpInfo;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use PiwikBundle\Service\NewTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ContentStatisticSender
 */
class ContentStatisticSender
{
    /**
     * @var NewTracker
     */
    private $newTracker;

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
     * @var Session
     */
    private $session;

    /**
     * @var CountryRepository
     */
    private $countryRepository;

    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;

    /**
     * ContentStatisticSender constructor
     *
     * @param NewTracker $newTracker
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     * @param MaxMindIpInfo $maxMindIpInfo
     * @param Session $session
     * @param CountryRepository $countryRepository
     * @param SubscriptionExtractor $subscriptionExtractor
     */
    public function __construct(
        NewTracker $newTracker,
        UserRepository $userRepository,
        LoggerInterface $logger,
        MaxMindIpInfo $maxMindIpInfo,
        Session $session,
        CountryRepository $countryRepository,
        SubscriptionExtractor $subscriptionExtractor
    ) {
        $this->newTracker = $newTracker;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->maxMindIpInfo = $maxMindIpInfo;
        $this->session = $session;
        $this->countryRepository = $countryRepository;
        $this->subscriptionExtractor = $subscriptionExtractor;
    }

    /**
     * @param ISPData $data
     *
     * @return bool
     */
    public function trackVisit(ISPData $data = null): bool
    {
        $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($this->session);
        $user = null;
        $country = null;

        if (!empty($identificationData['identification_token'])) {
            $token = $identificationData['identification_token'];

            /** @var User $user */
            $user = $this->userRepository->findOneBy(['identificationToken' => $token]);
            $country = $this->countryRepository->findOneBy(['countryCode' => $user->getCountry()]);
        }

        $userIp = $this->getUserIp();
        $connection = $this->maxMindIpInfo->getConnectionType();
        $operator = $data ? $data->getCarrierId() : null;

        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'pageVisit'
            ]);

            $result = $this->newTracker->trackPage(
                $user,
                $connection,
                $operator,
                $country,
                $userIp
            );

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage(), 'line' => $ex->getLine(), 'code' => $ex->getCode()]);

            return false;
        }
    }

    /**
     * @param Subscription $subscription
     * @param Game $game
     *
     * @return bool
     */
    public function trackDownload(Subscription $subscription, Game $game): bool
    {
        $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($this->session);
        $user = null;

        if (!empty($identificationData['identification_token'])) {
            $token = $identificationData['identification_token'];

            /** @var User $user */
            $user = $this->userRepository->findOneBy(['identificationToken' => $token]);
        }

        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => $this->newTracker::TRACK_DOWNLOAD
            ]);

            $result = $this->newTracker->trackDownload(
                $user,
                $game,
                $subscription
            );

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage(), 'line' => $ex->getLine(), 'code' => $ex->getCode()]);

            return false;
        }
    }

    /**
     * @param UploadedVideo $uploadedVideo
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function trackPlayingVideo(UploadedVideo $uploadedVideo): bool
    {
        $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($this->session);
        $subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($this->session);
        $user = null;

        if (!empty($identificationData['identification_token'])) {
            $token = $identificationData['identification_token'];

            /** @var User $user */
            $user = $this->userRepository->findOneBy(['identificationToken' => $token]);
        }

        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => $this->newTracker::TRACK_PLAYING_VIDEO
            ]);

            $result = $this->newTracker->trackVideoPlaying(
                $user,
                $uploadedVideo,
                $subscription
            );

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
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }
}