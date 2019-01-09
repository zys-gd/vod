<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.18
 * Time: 14:45
 */

namespace SubscriptionBundle\Service;


use SubscriptionBundle\Entity\SubscribedGame;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscribedGameRepository;

class SubscribedGameProvider
{
    /**
     * @var SubscribedGameRepository
     */
    private $subscribedGameRepository;

    /**
     * SubscribedGameProvider constructor.
     * @param SubscribedGameRepository $gameRepository
     */
    public function __construct(SubscribedGameRepository $gameRepository)
    {
        $this->subscribedGameRepository = $gameRepository;
    }

    /**
     * Check if the user has subscription products
     *
     * @param Subscription $subscription
     * @return SubscribedGame[]
     */

    public function getGamesSubscribed(Subscription $subscription)
    {
        return $this->subscribedGameRepository->findBy(['subscription' => $subscription]);
    }

}