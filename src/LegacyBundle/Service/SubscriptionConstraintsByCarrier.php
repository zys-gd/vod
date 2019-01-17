<?php

namespace LegacyBundle\Service;

use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\Subscription;

class SubscriptionConstraintsByCarrier
{

    /** @var EntityManager */
    protected $entityManager;
    /** @var CarrierRepository */
    private $carrierRepository;

    public function __construct(EntityManagerInterface $entityManager, CarrierRepository $carrierRepository)
    {
        $this->carrierRepository = $carrierRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Subscription $subscriptionEntity
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleCarrier(Subscription $subscriptionEntity)
    {
        /** @var Carrier $carrier */
        $carrier = $subscriptionEntity->getUser()->getCarrier();
        if (!empty($carrier->getRedirectUrl()) && $carrier->getnumberOfAllowedSubscriptionsByConstraint() !== null) {
            $carrier = $this->updateDate($carrier);
            $this->increaseCounter($carrier);
        }
    }

    /**
     * @param Carrier $carrier
     *
     * @return Carrier
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateDate(Carrier $carrier): Carrier
    {
        $this->carrierRepository->updateDate($carrier);
        $this->entityManager->refresh($carrier);

        return $carrier;
    }

    /**
     * @param Carrier $carrier
     */
    private function increaseCounter(Carrier $carrier)
    {
        $this->carrierRepository->increaseCounter($carrier);
    }
}
