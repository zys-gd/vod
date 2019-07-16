<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.05.18
 * Time: 10:02
 */

namespace IdentificationBundle\Profiler;


use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class IdentDataCollector extends DataCollector
{
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * IdentDataCollector constructor.
     * @param IdentificationDataStorage $dataStorage
     * @param SubscriptionRepository    $subscriptionRepository
     * @param UserRepository            $userRepository
     */
    public function __construct(
        IdentificationDataStorage $dataStorage,
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository
    )
    {
        $this->dataStorage            = $dataStorage;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository         = $userRepository;
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
            'wifi_flow'      => (int) $this->dataStorage->isWifiFlow(),
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