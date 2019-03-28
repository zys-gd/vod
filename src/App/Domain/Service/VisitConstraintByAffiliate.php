<?php

namespace App\Domain\Service;

use App\Domain\Entity\Campaign;
use App\Domain\Repository\CarrierRepository;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\AffiliateConstraint\ConstraintByAffiliateRedis;
use SubscriptionBundle\Service\Notification\Email\CAPNotificationSender;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @var ConstraintByAffiliateRedis
     */
    protected $constraintByAffiliateRedis;

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
     * @param ConstraintByAffiliateRedis $constraintByAffiliateRedis
     * @param CarrierRepository $carrierRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintByAffiliateRedis $constraintByAffiliateRedis,
        CarrierRepository $carrierRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->notificationSender = $notificationSender;
        $this->constraintByAffiliateRedis = $constraintByAffiliateRedis;
        $this->carrierRepository = $carrierRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Campaign $campaign
     *
     * @param SessionInterface $session
     *
     * @return RedirectResponse|null
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handleLandingPageRequest(Campaign $campaign, SessionInterface $session): ?RedirectResponse
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

            $isLimitReached = $this->constraintByAffiliateRedis->hasCounter($constraint)
                ? $this->constraintByAffiliateRedis->getCounter($constraint) >= $constraint->getNumberOfActions()
                : false;

            if ($isLimitReached) {
                if (!$constraint->getIsCapAlertDispatch()) {
                    $this->sendNotification($constraint, $carrier);
                }

                return new RedirectResponse($constraint->getRedirectUrl());
            } elseif ($constraint->getCapType() === ConstraintByAffiliate::CAP_TYPE_VISIT) {
                $this->constraintByAffiliateRedis->updateCounter($constraint);
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
        $result = $this->notificationSender->sendNotification($constraint, $carrier);

        if ($result) {
            $constraint->setIsCapAlertDispatch(true);

            $this->entityManager->persist($constraint);
            $this->entityManager->flush();
        }
    }
}