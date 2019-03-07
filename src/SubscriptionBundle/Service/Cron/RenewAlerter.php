<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.03.19
 * Time: 12:45
 */

namespace SubscriptionBundle\Service\Cron;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\Action\Renew\Handler\HasRenewAlerts;
use SubscriptionBundle\Service\Action\Renew\Handler\RenewHandlerProvider;
use SubscriptionBundle\Service\EntitySaveHelper;

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
     * RenewAlerter constructor.
     * @param SubscriptionRepository $repository
     * @param RenewHandlerProvider   $renewHandlerProvider
     * @param EntitySaveHelper       $entitySaveHelper
     */
    public function __construct(SubscriptionRepository $repository, RenewHandlerProvider $renewHandlerProvider, EntitySaveHelper $entitySaveHelper)
    {
        $this->repository           = $repository;
        $this->renewHandlerProvider = $renewHandlerProvider;
        $this->entitySaveHelper     = $entitySaveHelper;
    }

    public function sendRenewAlerts(CarrierInterface $carrier)
    {

        $renewer = $this->renewHandlerProvider->getRenewer($carrier);

        $subscriptions = $this->repository->findExpiringTomorrowSubscriptions($carrier);

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

        } else {
            throw new \InvalidArgumentException(
                sprintf('Carrier `%s` does not support Renew Alerts', $carrier->getBillingCarrierId())
            );
        }

    }
}