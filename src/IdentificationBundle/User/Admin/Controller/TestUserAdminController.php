<?php

namespace IdentificationBundle\User\Admin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\TestUser;
use IdentificationBundle\Repository\TestUserRepository;
use IdentificationBundle\Repository\UserRepository;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use Sonata\AdminBundle\Controller\CRUDController;
use SubscriptionBundle\BillingFramework\Process\DeleteProcess;
use SubscriptionBundle\Entity\Subscription;
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
     * @var ApiConnector
     */
    private $apiConnector;
    /**
     * @var DeleteProcess
     */
    private $deleteProcess;

    /**
     * TestUserAdminController constructor
     *
     * @param UserRepository         $userRepository
     * @param TestUserRepository     $testUserRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param EntityManagerInterface $entityManager
     * @param ApiConnector           $apiConnector
     * @param DeleteProcess          $deleteProcess
     */
    public function __construct(
        UserRepository $userRepository,
        TestUserRepository $testUserRepository,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager,
        ApiConnector $apiConnector,
        DeleteProcess $deleteProcess
    )
    {
        $this->userRepository         = $userRepository;
        $this->testUserRepository     = $testUserRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager          = $entityManager;
        $this->apiConnector           = $apiConnector;
        $this->deleteProcess          = $deleteProcess;
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
        $testUser       = $this->testUserRepository->find($id);
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
     *
     * @throws \Exception
     */
    public function setStatusForRenewAction(string $id)
    {
        /** @var TestUser $testUser */
        $testUser       = $this->testUserRepository->find($id);
        $userIdentifier = $testUser->getUserIdentifier();

        $user = $this->userRepository->findOneBy(['identifier' => $userIdentifier]);

        $response = new RedirectResponse($this->generateUrl('admin_identification_testuser_list'));

        if (!$user) {
            $this->addFlash('error', sprintf('User with identifier %s not found', $userIdentifier));
            return $response;
        }

        /** @var Subscription $subscription */
        $subscription = $this->subscriptionRepository->findOneBy(['user' => $user]);

        if (!$subscription) {
            $this->addFlash('error', sprintf('Subscription for user with identifier %s not found', $userIdentifier));

            return $response;
        }

        $subscription->setRenewDate(new \DateTime('-1 day'));

        $testUser->setLastTimeUsedAt(new \DateTime());

        $this->entityManager->persist($testUser);
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Subscription for user with identifier %s queued for renew', $userIdentifier));

        return $response;
    }


    public function cleanFromCrossSubscriptionAction(string $id)
    {
        /** @var TestUser $testUser */
        $testUser = $this->testUserRepository->find($id);


        $response = new RedirectResponse($this->generateUrl('admin_identification_testuser_list'));

        $msisdn    = $testUser->getUserIdentifier();
        $carrierId = $testUser->getCarrier()->getBillingCarrierId();

        if (!$this->apiConnector->checkIfExists($msisdn, $carrierId)) {
            $this->addFlash('error', sprintf('Msisdn "%s" for carrier "%s" is not exists or service is not available.', $msisdn, $carrierId));
            return $response;

        }

        $this->apiConnector->deregisterSubscription($msisdn, $carrierId);
        $this->addFlash('success', sprintf('Request to remove msisdn "%s" for carrier "%s" has been successfully sent.', $msisdn, $carrierId));

        return $response;

    }

    /**
     * @param string $id
     *
     * @return RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function dropFromBillingAction(string $id)
    {
        /** @var TestUser $testUser */
        $testUser       = $this->testUserRepository->find($id);
        $userIdentifier = $testUser->getUserIdentifier();

        $response = new RedirectResponse($this->generateUrl('admin_identification_testuser_list'));

        $this->deleteProcess->doDelete($userIdentifier);

        $testUser->setLastTimeUsedAt(new \DateTime());

        $this->entityManager->persist($testUser);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Processes for user with identifier "%s" dropped successfully', $userIdentifier));

        return $response;
    }

}