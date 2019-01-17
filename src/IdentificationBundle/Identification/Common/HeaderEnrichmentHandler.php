<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 10:15
 */

namespace IdentificationBundle\Identification\Common;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Handler\HasHeaderEnrichment;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\UserRepository;
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
     * @var IdentificationStatus
     */
    private $identificationStatus;


    /**
     * HeaderEnrichmentHandler constructor.
     * @param UserFactory            $userFactory
     * @param EntityManagerInterface $entityManager
     * @param UserRepository         $userRepository
     * @param IdentificationStatus   $identificationStatus
     */
    public function __construct(
        UserFactory $userFactory,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        IdentificationStatus $identificationStatus
    )
    {
        $this->userFactory          = $userFactory;
        $this->entityManager        = $entityManager;
        $this->userRepository       = $userRepository;
        $this->identificationStatus = $identificationStatus;
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

        $user = $this->userRepository->findOneByMsisdn($msisdn);
        if (!$user) {
            $user = $this->userFactory->create(
                $msisdn,
                $carrier,
                $request->getClientIp(),
                $token
            );
            $this->entityManager->persist($user);
        } else {
            $user->setIdentificationToken($token);
        }

        $this->entityManager->flush();
        $this->identificationStatus->finishIdent($token, $user);

    }
}