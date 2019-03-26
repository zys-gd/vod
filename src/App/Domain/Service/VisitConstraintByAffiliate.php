<?php

namespace App\Domain\Service;

use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
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
     * @var Carrier
     */
    protected $carrier;

    /**
     * AbstractConstraintByAffiliateService constructor
     *
     * @param CAPNotificationSender $notificationSender
     * @param ConstraintByAffiliateCache $cache
     * @param CarrierRepository $carrierRepository
     * @param SessionInterface $session
     */
    public function __construct(
        CAPNotificationSender $notificationSender,
        ConstraintByAffiliateCache $cache,
        CarrierRepository $carrierRepository,
        SessionInterface $session
    ) {
        $this->notificationSender = $notificationSender;
        $this->cache = $cache;

        $ispDetectionData = $session->get('isp_detection_data');

        if (!empty($ispDetectionData['carrier_id'])) {
            $this->carrier = $this->carrier = $carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);
        }
    }

    /**
     * @param Campaign $campaign
     *
     * @return RedirectResponse|null
     */
    public function handleLandingPageRequest(Campaign $campaign)
    {
        $affiliate = $campaign->getAffiliate();
        $constraints = $affiliate->getConstraints();

        /** @var ConstraintByAffiliate $constraint */
        foreach ($constraints as $constraint) {
            if (!$this->carrier
                || ($this->carrier && $this->carrier->getUuid() !== $constraint->getCarrier()->getUuid())
            ) {
                continue;
            }

            $isLimitReached = $this->cache->hasCachedCounter($constraint)
                ?? $this->cache->getCachedCounter($constraint) >= $constraint->getNumberOfActions();

            if ($isLimitReached) {
                return new RedirectResponse($constraint->getRedirectUrl());
            } elseif ($constraint->getCapType() === ConstraintByAffiliate::CAP_TYPE_VISIT) {
                $this->cache->updateCounter($constraint);
            }
        }

        return null;
    }
}