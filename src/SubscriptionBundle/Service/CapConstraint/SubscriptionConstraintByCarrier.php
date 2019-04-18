<?php

namespace SubscriptionBundle\Service\CapConstraint;

use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Service\Notification\Email\CAPNotificationSender;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SubscriptionConstraintByCarrier
 */
class SubscriptionConstraintByCarrier
{
    /**
     * @var CAPNotificationSender
     */
    private $notificationSender;

    /**
     * @var ConstraintCounterRedis
     */
    private $constraintCounterRedis;

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SubscriptionConstraintByCarrier constructor
     *
     * @param CAPNotificationSender $notificationSender
     * @param ConstraintCounterRedis $constraintCounterRedis
     * @param CarrierRepositoryInterface $carrierRepository
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintCounterRedis $constraintCounterRedis,
        CarrierRepositoryInterface $carrierRepository,
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ) {
        $this->notificationSender = $notificationSender;
        $this->constraintCounterRedis = $constraintCounterRedis;
        $this->carrierRepository = $carrierRepository;
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * @param CarrierInterface|null $carrier
     * @return bool|null
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function isSubscriptionLimitReached(CarrierInterface $carrier = null): ?bool
    {
        if (!$carrier) {
            $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);

            if (empty($ispDetectionData['carrier_id'])) {
                return false;
            }

            $carrier =  $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);
        }

        $allowedSubscriptions = $carrier->getNumberOfAllowedSubscriptionsByConstraint();

        if (empty($allowedSubscriptions)) {
            return false;
        }

        $counter = $this->constraintCounterRedis->getCounter($carrier->getBillingCarrierId());

        $isLimitReached = $counter ? $counter >= $allowedSubscriptions : false;

        if ($isLimitReached) {
            if (!$carrier->getIsCapAlertDispatch()) {
                $this->sendNotification($carrier);
            }

            return true;
        }

        return false;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function sendNotification(CarrierInterface $carrier)
    {
        $result = $this->notificationSender->sendCapByCarrierNotification($carrier);

        if ($result) {
            $carrier->setIsCapAlertDispatch(true);

            $this->entityManager->persist($carrier);
            $this->entityManager->flush();
        }
    }
}