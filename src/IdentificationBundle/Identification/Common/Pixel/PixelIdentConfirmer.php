<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 20:17
 */

namespace IdentificationBundle\Identification\Common\Pixel;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\BillingFramework\Data\DataProvider;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Handler\CommonFlow\HasCustomPixelIdent;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class PixelIdentConfirmer
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var DataProvider
     */
    private $dataProvider;
    /**
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;


    /**
     * PixelIdentConfirmer constructor.
     * @param EntityManagerInterface        $entityManager
     * @param UserFactory                   $userFactory
     * @param CarrierRepositoryInterface    $carrierRepository
     * @param DataProvider                  $dataProvider
     * @param IdentificationHandlerProvider $identificationHandlerProvider
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserFactory $userFactory,
        CarrierRepositoryInterface $carrierRepository,
        DataProvider $dataProvider,
        IdentificationHandlerProvider $identificationHandlerProvider

    )
    {
        $this->entityManager                 = $entityManager;
        $this->userFactory                   = $userFactory;
        $this->carrierRepository             = $carrierRepository;
        $this->dataProvider                  = $dataProvider;
        $this->identificationHandlerProvider = $identificationHandlerProvider;
    }

    public function confirmIdent(string $processId, array $identificationData): void
    {
        $carrierId           = $identificationData['carrier_id'] ?? null;
        $identificationToken = $identificationData['identification_token'] ?? null;

        $result = $this->dataProvider->getProcessData($processId);
        if (!$result->isSuccessful()) {
            throw  new \RuntimeException('Identification is not finished yet');
        }

        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $handler = $this->identificationHandlerProvider->get($carrier);
        if ($handler instanceof HasCustomPixelIdent) {
            $handler->onConfirm($result);
        }

        $this->saveUser($processId, $result, $carrier, $identificationToken);

    }

    /**
     * @param string $processId
     * @param        $result
     * @param        $carrier
     * @param        $identificationToken
     */
    private function saveUser(string $processId, ProcessResult $result, CarrierInterface $carrier, string $identificationToken): void
    {
        $clientFields = $result->getClientFields();
        $user         = $this->userFactory->create(
            $result->getProviderUser(),
            $carrier,
            $clientFields['user_ip'],
            $identificationToken,
            $processId
        );

        $this->entityManager->persist($user);
    }
}