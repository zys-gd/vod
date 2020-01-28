<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 15:53
 */

namespace IdentificationBundle\Callback;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\HasCustomFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerProvider;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\Handler\HasPostPaidRestriction;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\User\Service\UserFactory;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\Exception\EmptyResponse;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;

class IdentCallbackProcessor
{
    /**
     * @var ProcessResponseMapper
     */
    private $mapper;
    /**
     * @var \IdentificationBundle\User\Service\UserFactory
     */
    private $userFactory;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var IdentCallbackHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var PostPaidHandler
     */
    private $postPaidHandler;


    /**
     * IdentCallbackProcessor constructor.
     *
     * @param ProcessResponseMapper                          $mapper
     * @param \IdentificationBundle\User\Service\UserFactory $userFactory
     * @param CarrierRepositoryInterface                     $carrierRepository
     * @param LoggerInterface                                $logger
     * @param EntityManagerInterface                         $entityManager
     * @param UserRepository                                 $userRepository
     * @param IdentCallbackHandlerProvider                   $handlerProvider
     * @param PostPaidHandler                                $postPaidHandler
     */
    public function __construct(
        ProcessResponseMapper $mapper,
        UserFactory $userFactory,
        CarrierRepositoryInterface $carrierRepository,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        IdentCallbackHandlerProvider $handlerProvider,
        PostPaidHandler $postPaidHandler
    )
    {
        $this->mapper            = $mapper;
        $this->userFactory       = $userFactory;
        $this->carrierRepository = $carrierRepository;
        $this->logger            = $logger;
        $this->entityManager     = $entityManager;
        $this->userRepository    = $userRepository;
        $this->handlerProvider   = $handlerProvider;
        $this->postPaidHandler = $postPaidHandler;
    }

    /**
     * @param string $type
     * @param int    $carrierId
     * @param array  $attributes
     *
     * @throws EmptyResponse
     * @throws \Exception
     */
    public function process(string $type, int $carrierId, array $attributes): void
    {
        if ($type !== IdentProcess::PROCESS_METHOD_IDENT) {
            throw new \InvalidArgumentException('invalid `type` parameter');
        }

        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $result  = $this->mapper->map($type, (object)$attributes);
        $handler = $this->handlerProvider->getHandler($carrierId);

        if ($handler instanceof HasCustomFlow) {
            $handler->process($result, $carrier);

        } elseif ($handler instanceof HasCommonFlow) {
            if ($result->isSuccessful()) {
                $user = $this->handleSuccess($result, $carrier);
                $handler->afterSuccess($user, $result);

                if ($handler instanceof HasPostPaidRestriction) {
                    $this->postPaidHandler->process($user->getIdentifier(), $carrier->getBillingCarrierId());
                }
            } else {

            }

        } else {
            throw new \RuntimeException('Handlers for identification callback should have according interfaces');
        }
    }

    /**
     * @param ProcessResult    $result
     * @param CarrierInterface $carrier
     *
     * @return User
     * @throws \Exception
     */
    private function handleSuccess(ProcessResult $result, CarrierInterface $carrier): User
    {
        $token        = $result->getClientId();
        $msisdn       = $result->getClientUser() ?? $result->getProviderUser();
        $processId    = $result->getId();
        $clientFields = $result->getClientFields();

        /** @var User $user */
        if (!$user = $this->userRepository->findOneBy(['identifier' => $msisdn])) {
            $user = $this->userFactory->create($msisdn, $carrier, $clientFields['user_ip'], $token, $processId );
            $this->entityManager->persist($user);
        } else {
            $user->setIdentificationToken($token);
            $user->setIdentificationProcessId($processId);
        }

        $this->entityManager->flush();

        return $user;
    }
}