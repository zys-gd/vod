<?php

namespace SubscriptionBundle\CAPTool\Subscription\Notificaton;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Email\EmailSender;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Class CAPNotificationSender
 */
class CAPNotificationSender
{
    const DEFAULT_AFFILIATE_SUBJECT = '[Alert] CAP by affiliate reached';
    const DEFAULT_CARRIER_SUBJECT = '[Alert] Subscription CAP by carrier reached';

    /**
     * @var array
     */
    private $affiliateSubjects = [
        ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE => '[Alert] Subscription CAP by affiliate reached',
        ConstraintByAffiliate::CAP_TYPE_VISIT     => '[Alert] Visit CAP by affiliate reached'
    ];

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var EmailProvider
     */
    private $emailsProvider;

    /**
     * CAPNotificationSender constructor
     *
     * @param EmailSender   $emailSender
     * @param EmailProvider $emailsProvider
     */
    public function __construct(
        EmailSender $emailSender,
        EmailProvider $emailsProvider
    )
    {
        $this->emailSender    = $emailSender;
        $this->emailsProvider = $emailsProvider;
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     * @param CarrierInterface      $carrier
     *
     * @return bool
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCapByAffiliateNotification(
        ConstraintByAffiliate $constraintByAffiliate,
        CarrierInterface $carrier
    ): bool
    {
        $twig = '@Subscription/CapNotifications/cap_by_affiliate_alert.html.twig';

        $capType = $constraintByAffiliate->getCapType();

        $data = [
            'affiliateName' => $constraintByAffiliate->getAffiliate()->getName(),
            'carrierName'   => $carrier->getName(),
            'actionsLimit'  => $constraintByAffiliate->getNumberOfActions(),
            'capType'       => $capType
        ];

        $subject = array_key_exists($capType, $this->affiliateSubjects)
            ? $this->affiliateSubjects[$capType]
            : self::DEFAULT_AFFILIATE_SUBJECT;

        return (bool)$this->send($twig, $data, $subject);
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCapByCarrierNotification(CarrierInterface $carrier): bool
    {
        $twig = '@Subscription/CapNotifications/cap_by_carrier_alert.html.twig';

        $data = [
            'carrierName'  => $carrier->getName(),
            'actionsLimit' => $carrier->getNumberOfAllowedSubscriptionsByConstraint()
        ];

        return (bool)$this->send($twig, $data, self::DEFAULT_CARRIER_SUBJECT);
    }

    /**
     * @param string $twig
     * @param array  $data
     * @param string $subject
     *
     * @return int
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function send(string $twig, array $data, string $subject)
    {
        return $this
            ->emailSender
            ->sendMessage($twig, $data, $subject, $this->emailsProvider->getEmailFrom(), $this->emailsProvider->getEmailTo());
    }
}