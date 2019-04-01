<?php

namespace App\Domain\Service;

use App\Domain\Entity\Campaign;
use App\Domain\Repository\CarrierRepository;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CapConstraint\ConstraintCounterRedis;
use SubscriptionBundle\Service\Notification\Email\CAPNotificationSender;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class VisitConstraintByAffiliateService
 */
class VisitConstraintByAffiliate
{
    /**
     * @var CAPNotificationSender
     */
    protected $notificationSender;

    /**
     * @var ConstraintCounterRedis
     */
    protected $constraintCounterRedis;

    /**
     * @var CarrierRepository
     */
    protected $carrierRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AbstractConstraintByAffiliateService constructor
     *
     * @param CAPNotificationSender $notificationSender
     * @param ConstraintCounterRedis $constraintCounterRedis
     * @param CarrierRepository $carrierRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintCounterRedis $constraintCounterRedis,
        CarrierRepository $carrierRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->notificationSender = $notificationSender;
        $this->constraintCounterRedis = $constraintCounterRedis;
        $this->carrierRepository = $carrierRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Campaign $campaign
     *
     * @param SessionInterface $session
     *
     * @return string|null
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function isConstraintsLimitReached(Campaign $campaign, SessionInterface $session): ?string
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($session);

        if (empty($ispDetectionData['carrier_id'])
            || !$carrier =  $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id'])
        ) {
            return null;
        }

        $affiliate = $campaign->getAffiliate();

        /** @var ConstraintByAffiliate $constraint */
        foreach ($affiliate->getConstraints()->getIterator() as $constraint) {
            if ($carrier && $carrier->getUuid() !== $constraint->getCarrier()->getUuid()) {
                continue;
            }

            $counter = $this->constraintCounterRedis->getCounter($constraint->getUuid());

            $isLimitReached = $counter ? $counter >= $constraint->getNumberOfActions() : false;

            if ($isLimitReached) {
                if (!$constraint->getIsCapAlertDispatch()) {
                    $this->sendNotification($constraint, $carrier);
                }

                return $constraint->getRedirectUrl();
            } elseif ($constraint->getCapType() === ConstraintByAffiliate::CAP_TYPE_VISIT) {
                $this->constraintCounterRedis->updateCounter($constraint->getUuid());
            }
        }

        return null;
    }

    /**
     * @param ConstraintByAffiliate $constraint
     * @param CarrierInterface $carrier
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function sendNotification(ConstraintByAffiliate $constraint, CarrierInterface $carrier)
    {
        $result = $this->notificationSender->sendCapByAffiliateNotification($constraint, $carrier);

        if ($result) {
            $constraint->setIsCapAlertDispatch(true);

            $this->entityManager->persist($constraint);
            $this->entityManager->flush();
        }
    }
}