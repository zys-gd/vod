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
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Exception\FailedIdentificationException;
use IdentificationBundle\Identification\Handler\HasHeaderEnrichment;
use IdentificationBundle\Identification\Handler\HasPostPaidRestriction;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
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
     * @var PostPaidHandler
     */
    private $postPaidHandler;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * HeaderEnrichmentHandler constructor.
     *
     * @param UserFactory            $userFactory
     * @param EntityManagerInterface $entityManager
     * @param UserRepository         $userRepository
     * @param IdentificationStatus   $identificationStatus
     * @param PostPaidHandler        $postPaidHandler
     * @param LoggerInterface        $logger
     */
    public function __construct(
        UserFactory $userFactory,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        IdentificationStatus $identificationStatus,
        PostPaidHandler $postPaidHandler,
        LoggerInterface $logger
    )
    {
        $this->userFactory          = $userFactory;
        $this->entityManager        = $entityManager;
        $this->userRepository       = $userRepository;
        $this->identificationStatus = $identificationStatus;
        $this->postPaidHandler      = $postPaidHandler;
        $this->logger               = $logger;
    }

    /**
     * @param Request             $request
     * @param HasHeaderEnrichment $handler
     * @param CarrierInterface    $carrier
     * @param string              $token
     * @param DeviceData          $deviceData
     *
     * @throws \Exception
     */
    public function process(Request $request,
        HasHeaderEnrichment $handler,
        CarrierInterface $carrier,
        string $token,
        DeviceData $deviceData): void
    {
        if (!$msisdn = $handler->getMsisdn($request)) {
            throw new FailedIdentificationException('Cannot retrieve msisdn');
        }

        $this->logger->debug('headers', [$request->headers->all()]);

        $user = $this->userRepository->findOneByMsisdn($msisdn);
        if (!$user) {
            $user = $this->userFactory->create(
                $msisdn, $carrier, $request->getClientIp(), $token, null, $deviceData
            );
            $this->entityManager->persist($user);
        }
        else {
            $user->setIdentificationToken($token);
        }

        if ($handler instanceof HasPostPaidRestriction) {
            $this->postPaidHandler->process($msisdn, $carrier->getBillingCarrierId());
        }

        $this->entityManager->flush();
        $this->identificationStatus->finishIdent($token, $user);

    }
}