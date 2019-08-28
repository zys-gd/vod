<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.05.19
 * Time: 16:45
 */

namespace SubscriptionBundle\CAPTool\Visit;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\CAPTool\Subscription\Notificaton\CAPNotificationSender;
use SubscriptionBundle\CAPTool\Visit\Exception\InvalidConstraintException;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\EntitySaveHelper;

class VisitNotifier
{
    /**
     * @var ConstraintValidityChecker
     */
    private $constraintValidityChecker;
    /**
     * @var \SubscriptionBundle\CAPTool\Subscription\Notificaton\CAPNotificationSender
     */
    private $notificationSender;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;


    /**
     * VisitNotifier constructor.
     * @param ConstraintValidityChecker                                             $constraintValidityChecker
     * @param \SubscriptionBundle\CAPTool\Subscription\Notificaton\CAPNotificationSender $notificationSender
     * @param EntitySaveHelper                                                      $entitySaveHelper
     */
    public function __construct(ConstraintValidityChecker $constraintValidityChecker, CAPNotificationSender $notificationSender, EntitySaveHelper $entitySaveHelper)
    {
        $this->constraintValidityChecker = $constraintValidityChecker;
        $this->notificationSender        = $notificationSender;
        $this->entitySaveHelper          = $entitySaveHelper;
    }

    public function notifyLimitReached(ConstraintByAffiliate $constraint, CarrierInterface $carrier): void
    {
        if (!$this->constraintValidityChecker->isValidConstraint($constraint)) {
            throw new InvalidConstraintException('Invalid constraint');
        }

        if (!$constraint->getIsCapAlertDispatch()) {
            $this->notificationSender->sendCapByAffiliateNotification($constraint, $carrier);
            $constraint->setIsCapAlertDispatch(true);
            $this->entitySaveHelper->persistAndSave($constraint);
        }

    }
}