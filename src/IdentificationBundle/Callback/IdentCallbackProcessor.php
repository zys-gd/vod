<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 15:53
 */

namespace IdentificationBundle\Callback;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\Callback\Handler\HasCommonFlow;
use IdentificationBundle\Callback\Handler\HasCustomFlow;
use IdentificationBundle\Callback\Handler\IdentCallbackHandlerProvider;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\Exception\EmptyResponse;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class IdentCallbackProcessor
{
    /**
     * @var ProcessResponseMapper
     */
    private $mapper;
    /**
     * @var \IdentificationBundle\Identification\Service\UserFactory
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
     * IdentCallbackProcessor constructor.
     * @param ProcessResponseMapper        $mapper
     * @param UserFactory                  $userFactory
     * @param CarrierRepositoryInterface   $carrierRepository
     * @param LoggerInterface              $logger
     * @param EntityManagerInterface       $entityManager
     * @param UserRepository               $userRepository
     * @param IdentCallbackHandlerProvider $handlerProvider
     */
    public function __construct(
        ProcessResponseMapper $mapper,
        UserFactory $userFactory,
        CarrierRepositoryInterface $carrierRepository,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        IdentCallbackHandlerProvider $handlerProvider
    )
    {
        $this->mapper            = $mapper;
        $this->userFactory       = $userFactory;
        $this->carrierRepository = $carrierRepository;
        $this->logger            = $logger;
        $this->entityManager     = $entityManager;
        $this->userRepository    = $userRepository;
        $this->handlerProvider   = $handlerProvider;
    }

    /**
     * @param string $type
     * @param int    $carrierId
     * @param array  $attributes
     * @throws BadRequestHttpException
     * @throws EmptyResponse
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
            } else {

            }

        } else {
            throw new \RuntimeException('Handlers for identification callback should have according interfaces');
        }
    }

    /**
     * @param $result
     * @param $carrier
     * @return User
     */
    private function handleSuccess(ProcessResult $result, CarrierInterface $carrier): User
    {
        $token        = $result->getClientId();
        $msisdn       = $result->getProviderUser();
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