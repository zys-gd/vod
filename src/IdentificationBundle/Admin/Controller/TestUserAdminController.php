<?php

namespace IdentificationBundle\Admin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\TestUser;
use IdentificationBundle\Repository\TestUserRepository;
use IdentificationBundle\Repository\UserRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function __construct(
        UserRepository $userRepository,
        TestUserRepository $testUserRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->testUserRepository = $testUserRepository;
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

        if (!$this->userRepository->findOneBy(['identifier' => $userIdentifier])) {
            throw new NotFoundHttpException(sprintf('User with identifier %s not found', $userIdentifier));
        }

        $this->userRepository->dropUserDataByIdentifier($userIdentifier);

        $testUser->setLastTimeUsedAt(new \DateTime());

        $this->entityManager->persist($testUser);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('User with identifier "%s" dropped successfully', $userIdentifier));

        return new RedirectResponse($this->generateUrl('admin_identification_testuser_list'));
    }

    public function setStatusForRenewAction(string $id)
    {

    }
}