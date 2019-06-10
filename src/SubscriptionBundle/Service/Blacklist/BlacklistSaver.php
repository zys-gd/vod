<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.05.19
 * Time: 14:34
 */

namespace SubscriptionBundle\Service\Blacklist;


use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Entity\BlackList;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Service\Action\Unsubscribe\Unsubscriber;

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
     * @var UnsubscriptionHandlerProvider
     */
    private $unsubscriptionHandlerProvider;
    /**
     * @var Unsubscriber
     */
    private $unsubscriber;

    /**
     * BlacklistSaver constructor.
     * @param UserRepository                $userRepository
     * @param EntityManagerInterface        $entityManager
     * @param SubscriptionRepository        $subscriptionRepository
     * @param UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider
     * @param Unsubscriber                  $unsubscriber
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SubscriptionRepository $subscriptionRepository,
        UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider,
        Unsubscriber $unsubscriber
    )
    {
        $this->userRepository                = $userRepository;
        $this->entityManager                 = $entityManager;
        $this->subscriptionRepository        = $subscriptionRepository;
        $this->unsubscriptionHandlerProvider = $unsubscriptionHandlerProvider;
        $this->unsubscriber                  = $unsubscriber;
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