<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.05.19
 * Time: 14:34
 */

namespace SubscriptionBundle\Blacklist;


use Doctrine\ORM\EntityManagerInterface;
use ExtrasBundle\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Entity\BlackList;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeFacade;

class BlacklistSaver
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var UnsubscribeFacade
     */
    private $unsubscribeFacade;

    /**
     * BlacklistSaver constructor.
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $entityManager
     * @param SubscriptionRepository $subscriptionRepository
     * @param UnsubscribeFacade      $unsubscribeFacade
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SubscriptionRepository $subscriptionRepository,
        UnsubscribeFacade $unsubscribeFacade
    )
    {
        $this->userRepository         = $userRepository;
        $this->entityManager          = $entityManager;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->unsubscribeFacade      = $unsubscribeFacade;
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

                $this->doAfterAddedToBlackList($blackList);

                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
    }


    /**
     * @param BlackList $blackList
     */
    public function doAfterAddedToBlackList(BlackList $blackList): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy([
            'identifier' => $blackList->getAlias()
        ]);

        if ($user) {
            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

            if ($subscription && $subscription->getCurrentStage() != Subscription::ACTION_UNSUBSCRIBE) {
                $this->unsubscribeFacade->doUnsubscribeWithoutDeregisterFromCrossSub($subscription);
            }
        }
    }
}