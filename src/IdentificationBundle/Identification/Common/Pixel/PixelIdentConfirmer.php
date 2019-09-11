<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 20:17
 */

namespace IdentificationBundle\Identification\Common\Pixel;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\BillingFramework\Data\DataProvider;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Handler\CommonFlow\HasCustomPixelIdent;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\HasPostPaidRestriction;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\User\Service\UserFactory;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class PixelIdentConfirmer
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var \IdentificationBundle\User\Service\UserFactory
     */
    private $userFactory;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var DataProvider
     */
    private $billingDataProvider;
    /**
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;
    /**
     * @var IdentificationStatus
     */
    private $identificationStatus;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PostPaidHandler
     */
    private $postPaidHandler;


    /**
     * PixelIdentConfirmer constructor.
     *
     * @param EntityManagerInterface        $entityManager
     * @param UserFactory                   $userFactory
     * @param CarrierRepositoryInterface    $carrierRepository
     * @param DataProvider                  $billingDataProvider
     * @param IdentificationHandlerProvider $identificationHandlerProvider
     * @param IdentificationStatus          $identificationStatus
     * @param TokenGenerator                $tokenGenerator
     * @param UserRepository                $userRepository
     * @param PostPaidHandler               $postPaidHandler
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserFactory $userFactory,
        CarrierRepositoryInterface $carrierRepository,
        DataProvider $billingDataProvider,
        IdentificationHandlerProvider $identificationHandlerProvider,
        IdentificationStatus $identificationStatus,
        TokenGenerator $tokenGenerator,
        UserRepository $userRepository,
        PostPaidHandler $postPaidHandler
    )
    {
        $this->entityManager                 = $entityManager;
        $this->userFactory                   = $userFactory;
        $this->carrierRepository             = $carrierRepository;
        $this->billingDataProvider           = $billingDataProvider;
        $this->identificationHandlerProvider = $identificationHandlerProvider;
        $this->identificationStatus          = $identificationStatus;
        $this->tokenGenerator                = $tokenGenerator;
        $this->userRepository                = $userRepository;
        $this->postPaidHandler               = $postPaidHandler;
    }

    public function confirmIdent(string $processId, int $carrierId, DeviceData $deviceData): void
    {
        $result = $this->billingDataProvider->getProcessData($processId);
        if (!$result->isSuccessful()) {
            throw  new \RuntimeException('Identification is not finished yet');
        }

        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $handler = $this->identificationHandlerProvider->get($carrier);
        /** @var HasCommonFlow $handler */
        if ($handler instanceof HasCustomPixelIdent) {
            $handler->onConfirm($result);
        }

        $msisdn = $result->getProviderUser();
        $user   = $handler->getExistingUser($msisdn);

        if ($user) {
            $identificationToken = $user->getIdentificationToken();
            $user->setLanguageCode($deviceData->getBrowserLanguage());

        } else {
            $identificationToken = $this->tokenGenerator->generateToken();
            $user                = $this->saveUser(
                $processId,
                $result,
                $carrier,
                $identificationToken,
                $deviceData
            );
        }

        if ($handler instanceof HasPostPaidRestriction) {
            $this->postPaidHandler->process($msisdn, $carrier->getBillingCarrierId());
        }

        $this->identificationStatus->finishIdent($identificationToken, $user);

    }

    /**
     * @param string           $processId
     * @param ProcessResult    $result
     * @param CarrierInterface $carrier
     * @param string           $identificationToken
     * @param DeviceData       $deviceData
     *
     * @return User
     * @throws \Exception
     */
    private function saveUser(string $processId, ProcessResult $result, CarrierInterface $carrier, string $identificationToken, DeviceData $deviceData): User
    {
        $clientFields = $result->getClientFields();
        $user         = $this->userFactory->create(
            $result->getProviderUser(), $carrier, $clientFields['user_ip'], $identificationToken, $processId, $deviceData
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}