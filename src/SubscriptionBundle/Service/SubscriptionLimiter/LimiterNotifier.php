<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\Notification\Email\CAPNotificationSender;

class LimiterNotifier
{
    /**
     * @var CAPNotificationSender
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
    public function notifyLimitReached(CarrierInterface $carrier): void
    {
        if (!$carrier->getIsCapAlertDispatch() && $this->notificationSender->sendCapByCarrierNotification($carrier)) {
            $carrier->setIsCapAlertDispatch(true);
            $this->entitySaveHelper->persistAndSave($carrier);
        }
    }
}