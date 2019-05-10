<?php

namespace SubscriptionBundle\Service;

use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Entity\BlackList;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\BlackListRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Service\Action\Unsubscribe\Unsubscriber;

/**
 * Class BlackListService
 */
class BlackListService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var BlackListRepository
     */
    private $blackListRepository;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var UnsubscriptionHandlerProvider
     */
    private $unsubscriptionHandlerProvider;

    /**
     * @var Unsubscriber
     */
    private $unsubscriber;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BlackListService constructor
     *
     * @param UserRepository                $userRepository
     * @param BlackListRepository           $blackListRepository
     * @param SubscriptionRepository        $subscriptionRepository
     * @param EntityManagerInterface        $entityManager
     * @param UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider
     * @param Unsubscriber                  $unsubscriber
     */
    public function __construct(
        UserRepository $userRepository,
        BlackListRepository $blackListRepository,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager,
        UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider,
        Unsubscriber $unsubscriber
    )
    {
        $this->userRepository                = $userRepository;
        $this->blackListRepository           = $blackListRepository;
        $this->subscriptionRepository        = $subscriptionRepository;
        $this->entityManager                 = $entityManager;
        $this->unsubscriptionHandlerProvider = $unsubscriptionHandlerProvider;
        $this->unsubscriber                  = $unsubscriber;
    }

    /**
     * @param string $sessionToken
     *
     * @return bool
     */
    public function isBlacklisted(string $sessionToken): bool
    {
        try {
            $user = $this->userRepository->findOneByIdentificationToken($sessionToken);
            /** @var BlackList $blackList */
            $blackList = $this->blackListRepository->findOneBy(['alias' => $user->getIdentifier()]);
            $today     = new \DateTime();
            if ($blackList->getDuration() > 0
                && $blackList->getBanStart() < $today
                && $today < $blackList->getBanEnd()
                || $blackList->getDuration() == 0
            ) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string $sessionToken
     *
     * @throws \Exception
     */
    public function addToBlackList(string $sessionToken): void
    {
        if (!empty($sessionToken)) {
            /** @var User $user */
            $user = $this->userRepository->findOneByIdentificationToken($sessionToken);

            if ($user) {
                $blackList = new BlackList(UuidGenerator::generate());
                $blackList
                    ->setAlias($user->getIdentifier())
                    ->setBillingCarrierId($user->getCarrier()->getBillingCarrierId())
                    ->setIsBlockedManually(false);

                $blackList->setDuration(0); // permanently

                $this->entityManager->persist($blackList);

                $this->postBlackListing($blackList);

                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
    }

    /**
     * @param BlackList $blackList
     */
    public function postBlackListing(BlackList $blackList): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['identifier' => $blackList->getAlias()]);

        if ($user) {
            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

            if ($subscription && $subscription->getCurrentStage() != Subscription::ACTION_UNSUBSCRIBE) {
                $unsubscriptionHandler = $this->unsubscriptionHandlerProvider->getUnsubscriptionHandler($user->getCarrier());

                $response = $this->unsubscriber->unsubscribe($subscription, $subscription->getSubscriptionPack());
                $unsubscriptionHandler->applyPostUnsubscribeChanges($subscription);

                if ($unsubscriptionHandler->isPiwikNeedToBeTracked($response)) {
                    $this->unsubscriber->trackEventsForUnsubscribe($subscription, $response);
                }
            }
        }
    }
}