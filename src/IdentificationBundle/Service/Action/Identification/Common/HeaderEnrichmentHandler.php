<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 10:15
 */

namespace IdentificationBundle\Service\Action\Identification\Common;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\Service\Action\Identification\Common\Exception\FailedIdentificationException;
use IdentificationBundle\Service\Action\Identification\Handler\HasHeaderEnrichment;
use Symfony\Component\HttpFoundation\Request;

class HeaderEnrichmentHandler
{
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * HeaderEnrichmentHandler constructor.
     * @param UserFactory            $userFactory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserFactory $userFactory, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->userFactory    = $userFactory;
        $this->entityManager  = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request             $request
     * @param HasHeaderEnrichment $handler
     * @param CarrierInterface    $carrier
     * @param string              $token
     * @throws FailedIdentificationException
     */
    public function process(Request $request, HasHeaderEnrichment $handler, CarrierInterface $carrier, string $token): void
    {
        if (!$msisdn = $handler->getMsisdn($request)) {
            throw new FailedIdentificationException('Cannot retrieve msisdn');
        }

        $user = $this->userRepository->findOneByIdentificationToken($token);
        if (!$user) {
            $user = $this->userFactory->create(
                $msisdn,
                $carrier,
                $request->getClientIp(),
                $token
            );
            $this->entityManager->persist($user);
        } else {
            $user->setIdentifier($token);
        }
        $this->entityManager->flush();
    }
}