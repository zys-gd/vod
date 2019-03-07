<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Renew;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Renew\Common\RenewAlertTypeProvider;
use SubscriptionBundle\Service\Action\Renew\Handler\HasCommonFlow;
use SubscriptionBundle\Service\Action\Renew\Handler\HasRenewAlerts;
use SubscriptionBundle\Service\Action\Renew\Handler\RenewHandlerInterface;
use SubscriptionBundle\Service\Notification\Notifier;
use SubscriptionBundle\Service\SubscriptionPackProvider;

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
     * @param Notifier                 $notifier
     * @param SubscriptionPackProvider $provider
     * @param RenewAlertTypeProvider   $typeProvider
     */
    public function __construct(Notifier $notifier, SubscriptionPackProvider $provider, RenewAlertTypeProvider $typeProvider)
    {
        $this->notifier     = $notifier;
        $this->provider     = $provider;
        $this->typeProvider = $typeProvider;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::TELENOR_PAKISTAN_DOT;
    }

    public function onSuccess(Subscription $subscription, int $processId): void
    {
        // TODO: Implement onSuccess() method.
    }

    public function onFailure(Subscription $subscription, string $errorText): void
    {
        // TODO: Implement onFailure() method.
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