<?php

namespace IdentificationBundle\Admin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\TestUser;
use IdentificationBundle\Repository\TestUserRepository;
use IdentificationBundle\Repository\UserRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use SubscriptionBundle\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class TestUserAdminController
 */
class TestUserAdminController extends CRUDController
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
     * @var TestUserRepository
     */
    private $testUserRepository;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * TestUserAdminController constructor
     *
     * @param UserRepository $userRepository
     * @param TestUserRepository $testUserRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UserRepository $userRepository,
        TestUserRepository $testUserRepository,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->testUserRepository = $testUserRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $id
     *
     * @return RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function dropUserDataAction(string $id)
    {
        /** @var TestUser $testUser */
        $testUser = $this->testUserRepository->find($id);
        $userIdentifier = $testUser->getUserIdentifier();

        $response = new RedirectResponse($this->generateUrl('admin_identification_testuser_list'));

        if (!$this->userRepository->findOneBy(['identifier' => $userIdentifier])) {
            $this->addFlash('error', sprintf('User with identifier %s not found', $userIdentifier));

            return $response;
        }

        $this->userRepository->dropUserDataByIdentifier($userIdentifier);

        $testUser->setLastTimeUsedAt(new \DateTime());

        $this->entityManager->persist($testUser);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('User with identifier "%s" dropped successfully', $userIdentifier));

        return $response;
    }

    /**
     * @param string $id
     *
     * @return RedirectResponse
     */
    public function setStatusForRenewAction(string $id)
    {
        /** @var TestUser $testUser */
        $testUser = $this->testUserRepository->find($id);
        $userIdentifier = $testUser->getUserIdentifier();

        $user = $this->userRepository->findOneBy(['identifier' => $userIdentifier]);

        $response = new RedirectResponse($this->generateUrl('admin_identification_testuser_list'));

        if (!$user) {
            $this->addFlash('error', sprintf('User with identifier %s not found', $userIdentifier));

            return $response;
        }

        $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

        if (!$subscription) {
            $this->addFlash('error', sprintf('Subscription for user with identifier %s not found', $userIdentifier));

            return $response;
        }

        //TODO apply renew

        $this->addFlash('success', sprintf('Subscription for user with identifier %s queued for renew', $userIdentifier));

        return $response;
    }
}