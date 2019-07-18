<?php

namespace IdentificationBundle\Profiler;

use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use SubscriptionBundle\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class IdentDataCollector
 */
class IdentDataCollector extends DataCollector
{
    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * IdentDataCollector constructor
     *
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     * @param SubscriptionRepository $subscriptionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository
    ) {
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository = $userRepository;
    }


    /**
     * Collects data for the given Request and Response.
     *
     * @param Request    $request A Request instance
     * @param Response   $response A Response instance
     * @param \Exception $exception An Exception instance
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $session = $request->getSession();

        $extractIdentificationData = IdentificationFlowDataExtractor::extractIdentificationData($session);
        if (isset($extractIdentificationData['identification_token'])) {
            $user = $this->userRepository->findOneByIdentificationToken($extractIdentificationData['identification_token']);
            if ($user) {
                $subscription = $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);
            }
        }

        $this->data['current_identity'] = [
            'isp'            => IdentificationFlowDataExtractor::extractIspDetectionData($session),
            'identification' => $extractIdentificationData,
            'wifi_flow'      => (int) $this->wifiIdentificationDataStorage->isWifiFlow(),
            'user'           => isset($user)
                ? [
                    'uuid'       => $user->getUuid(),
                    'identifier' => $user->getIdentifier(),
                ]
                : null,
            'subscription'   => isset($subscription)
                ? [
                    'uuid'   => $subscription->getUuid(),
                    'status' => $subscription->getStatus(),
                    'stage'  => $subscription->getCurrentStageLabel()
                ]
                : null
        ];

    }

    public function getCurrentIdentity()
    {
        return $this->data['current_identity'];
    }


    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'identification.ident_data_collector';
    }


    public function reset()
    {
        $this->data['current_identity'] = null;
    }
}