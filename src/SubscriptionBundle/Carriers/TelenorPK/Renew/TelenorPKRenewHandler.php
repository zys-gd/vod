<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Renew;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Renew\Common\RenewAlertTypeProvider;
use SubscriptionBundle\Subscription\Renew\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Renew\Handler\HasRenewAlerts;
use SubscriptionBundle\Subscription\Renew\Handler\RenewHandlerInterface;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;

class TelenorPKRenewHandler implements
    RenewHandlerInterface,
    HasCommonFlow,
    HasRenewAlerts
{
    /**
     * @var Notifier
     */
    private $notifier;
    /**
     * @var SubscriptionPackProvider
     */
    private $provider;
    /**
     * @var RenewAlertTypeProvider
     */
    private $typeProvider;


    /**
     * TelenorPKRenewHandler constructor.
     * @param Notifier                                                      $notifier
     * @param \SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider $provider
     * @param RenewAlertTypeProvider                                        $typeProvider
     */
    public function __construct(Notifier $notifier, SubscriptionPackProvider $provider, RenewAlertTypeProvider $typeProvider)
    {
        $this->notifier     = $notifier;
        $this->provider     = $provider;
        $this->typeProvider = $typeProvider;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TELENOR_PAKISTAN_DOT;
    }

    public function onRenewSendSuccess(Subscription $subscription, int $processId): void
    {
        // TODO: Implement onRenewSendSuccess() method.
    }

    public function onRenewSendFailure(Subscription $subscription, string $errorText): void
    {
        // TODO: Implement onRenewSendFailure() method.
    }

    public function onRenewAlert(Subscription $subscription, CarrierInterface $carrier): void
    {
        $pack = $this->provider->getActiveSubscriptionPackFromCarrier($carrier);

        $this->notifier->sendNotification(
            $this->typeProvider->getForSubscriptionPack($pack),
            $subscription,
            $pack,
            $carrier
        );
    }
}