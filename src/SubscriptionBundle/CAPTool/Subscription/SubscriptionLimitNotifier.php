<?php


namespace SubscriptionBundle\CAPTool\Subscription;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\CAPTool\Subscription\Notificaton\CAPNotificationSender;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Service\EntitySaveHelper;

class SubscriptionLimitNotifier
{
    /**
     * @var \SubscriptionBundle\CAPTool\Subscription\Notificaton\CAPNotificationSender
     */
    private $notificationSender;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    public function __construct(CAPNotificationSender $notificationSender, EntitySaveHelper $entitySaveHelper)
    {
        $this->notificationSender = $notificationSender;
        $this->entitySaveHelper   = $entitySaveHelper;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function notifyLimitReachedForCarrier(CarrierInterface $carrier): void
    {
        if ($carrier->getIsCapAlertDispatch()) {
            return;
        }

        $carrier->setIsCapAlertDispatch(true);
        $this->entitySaveHelper->persistAndSave($carrier);

        if (!$this->notificationSender->sendCapByCarrierNotification($carrier)) {
            $carrier->setIsCapAlertDispatch(false);
            $this->entitySaveHelper->persistAndSave($carrier);
        }
    }

    public function notifyLimitReachedByAffiliate(ConstraintByAffiliate $constraintByAffiliate, CarrierInterface $carrier): void
    {

        if ($constraintByAffiliate->getIsCapAlertDispatch()) {
            return;
        }

        $constraintByAffiliate->setIsCapAlertDispatch(true);
        $this->entitySaveHelper->persistAndSave($constraintByAffiliate);

        if (!$this->notificationSender->sendCapByAffiliateNotification($constraintByAffiliate, $carrier)) {
            $constraintByAffiliate->setIsCapAlertDispatch(false);
            $this->entitySaveHelper->persistAndSave($constraintByAffiliate);
        }
    }
}