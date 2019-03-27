<?php

namespace App\Domain\Service;

use App\Domain\Entity\Campaign;
use App\Domain\Repository\CarrierRepository;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\AffiliateConstraint\ConstraintByAffiliateCache;
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
     * @var ConstraintByAffiliateCache
     */
    protected $cache;

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
     * @param ConstraintByAffiliateCache $cache
     * @param CarrierRepository $carrierRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintByAffiliateCache $cache,
        CarrierRepository $carrierRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->notificationSender = $notificationSender;
        $this->cache = $cache;
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
        $ispDetectionData = $session->get('isp_detection_data');

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

            $isLimitReached = $this->cache->hasCounter($constraint)
                ?? $this->cache->getCounter($constraint) >= $constraint->getNumberOfActions();

            if ($isLimitReached) {
                if (!$constraint->getIsCapAlertDispatch()) {
                    $this->sendNotification($constraint, $carrier);
                }

                return new RedirectResponse($constraint->getRedirectUrl());
            } elseif ($constraint->getCapType() === ConstraintByAffiliate::CAP_TYPE_VISIT) {
                $this->cache->updateCounter($constraint);
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