<?php

namespace SubscriptionBundle\Service\Notification\Email;

use ExtrasBundle\Email\EmailSender;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class CAPNotificationSender
 */
class CAPNotificationSender
{
    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var string
     */
    private $notificationMailTo;

    /**
     * @var string
     */
    private $notificationMailFrom;

    /**
     * CAPNotificationSender constructor
     *
     * @param EmailSender $emailSender
     * @param string $notificationMailTo
     * @param string $notificationMailFrom
     */
    public function __construct(
        EmailSender $emailSender,
        string $notificationMailTo,
        string $notificationMailFrom
    ) {
        $this->emailSender = $emailSender;
        $this->notificationMailTo = $notificationMailTo;
        $this->notificationMailFrom = $notificationMailFrom;
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     * @param CarrierInterface $carrier
     *
     * @return bool
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendNotification(ConstraintByAffiliate $constraintByAffiliate, CarrierInterface $carrier): bool
    {
        $twig = '@Subscription/ConstraintByAffiliate/Mail/cap_alert_template.html.twig';
        $data = [
            'affiliateName' => $constraintByAffiliate->getAffiliate()->getName(),
            'carrierName' => $carrier->getName(),
            'actionsLimit' => $constraintByAffiliate->getNumberOfActions(),
            'capType' => $constraintByAffiliate->getCapType()
        ];

        return (bool) $this
            ->emailSender
            ->sendMessage($twig, $data, $this->notificationMailFrom, $this->notificationMailTo);
    }
}