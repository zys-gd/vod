<?php

namespace App\Domain\Service;

use App\Domain\Repository\CountryRepository;
use CountryCarrierDetectionBundle\Service\MaxMindIpInfo;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use PiwikBundle\Service\NewTracker;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class PageVisitTracker
 */
class PageVisitTracker
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
     * PageVisitTracker constructor
     *
     * @param NewTracker $newTracker
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     * @param MaxMindIpInfo $maxMindIpInfo
     * @param Session $session
     * @param CountryRepository $countryRepository
     */
    public function __construct(
        NewTracker $newTracker,
        UserRepository $userRepository,
        LoggerInterface $logger,
        MaxMindIpInfo $maxMindIpInfo,
        Session $session,
        CountryRepository $countryRepository
    ) {
        $this->newTracker = $newTracker;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->maxMindIpInfo = $maxMindIpInfo;
        $this->session = $session;
        $this->countryRepository = $countryRepository;
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
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage()]);

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