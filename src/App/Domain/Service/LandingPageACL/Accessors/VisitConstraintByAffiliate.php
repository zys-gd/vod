<?php

namespace App\Domain\Service\LandingPageACL\Accessors;

use App\Domain\Entity\Campaign;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\CapConstraint\ConstraintCounterRedis;
use SubscriptionBundle\Service\Notification\Email\CAPNotificationSender;

/**
 * Class VisitConstraintByAffiliateService
 */
class VisitConstraintByAffiliate
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AbstractConstraintByAffiliateService constructor
     *
     * @param CAPNotificationSender $notificationSender
     * @param ConstraintCounterRedis $constraintCounterRedis
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintCounterRedis $constraintCounterRedis,
        EntityManagerInterface $entityManager
    ) {
        $this->notificationSender = $notificationSender;
        $this->constraintCounterRedis = $constraintCounterRedis;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Campaign $campaign
     *
     * @param CarrierInterface $carrier
     * @return bool
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function canVisit(Campaign $campaign, CarrierInterface $carrier): bool
    {
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

                return false;
            } elseif ($constraint->getCapType() === ConstraintByAffiliate::CAP_TYPE_VISIT) {
                $this->constraintCounterRedis->updateCounter($constraint->getUuid());
            }
        }

        return true;
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