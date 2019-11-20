<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.03.19
 * Time: 12:45
 */

namespace SubscriptionBundle\Subscription\Renew\Service;


use App\Domain\Repository\CarrierRepository;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Renew\Handler\HasRenewAlerts;
use SubscriptionBundle\Subscription\Renew\Handler\RenewHandlerProvider;

class RenewAlerter
{
    /**
     * @var SubscriptionRepository
     */
    private $repository;
    /**
     * @var RenewHandlerProvider
     */
    private $renewHandlerProvider;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;


    /**
     * RenewAlerter constructor.
     *
     * @param SubscriptionRepository $repository
     * @param RenewHandlerProvider   $renewHandlerProvider
     * @param EntitySaveHelper       $entitySaveHelper
     * @param CarrierRepository      $carrierRepository
     */
    public function __construct(
        SubscriptionRepository $repository,
        RenewHandlerProvider $renewHandlerProvider,
        EntitySaveHelper $entitySaveHelper,
        CarrierRepository $carrierRepository
    )
    {
        $this->repository           = $repository;
        $this->renewHandlerProvider = $renewHandlerProvider;
        $this->entitySaveHelper     = $entitySaveHelper;
        $this->carrierRepository    = $carrierRepository;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @throws \Exception
     */
    public function sendRenewAlerts(CarrierInterface $carrier)
    {

        $renewer = $this->renewHandlerProvider->getRenewer($carrier);

        $subPack       = $this->carrierRepository->findActiveSubscriptionPack($carrier);
        $subscriptions = $this->repository->findExpiringTomorrowSubscriptions($carrier, $subPack);

        if ($renewer instanceof HasRenewAlerts) {
            foreach ($subscriptions as $subscription) {
                try {
                    $renewer->onRenewAlert($subscription, $carrier);
                } catch (\Error $exception) {
                } finally {
                    $subscription->setLastRenewAlertDate(new \DateTime());
                }
            }
            $this->entitySaveHelper->saveAll();

        }
        else {
            throw new \InvalidArgumentException(
                sprintf('Carrier `%s` does not support Renew Alerts', $carrier->getBillingCarrierId())
            );
        }

    }
}