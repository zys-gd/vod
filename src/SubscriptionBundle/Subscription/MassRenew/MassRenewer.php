<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.03.19
 * Time: 15:22
 */

namespace SubscriptionBundle\Subscription\MassRenew;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\MassRenewProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Renew\DTO\MassRenewResult;
use SubscriptionBundle\Subscription\Renew\OnRenewUpdater;

class MassRenewer
{
    /**
     * @var MassRenewProcess
     */
    private $massRenewProcess;
    /**
     * @var MassRenewParametersProvider
     */
    private $parametersProvider;
    /**
     * @var OnRenewUpdater
     */
    private $onRenewUpdater;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;


    /**
     * MassRenewer constructor.
     * @param MassRenewProcess            $massRenewProcess
     * @param MassRenewParametersProvider $parametersProvider
     * @param OnRenewUpdater              $onRenewUpdater
     * @param EntitySaveHelper            $entitySaveHelper
     */
    public function __construct(
        MassRenewProcess $massRenewProcess,
        MassRenewParametersProvider $parametersProvider,
        OnRenewUpdater $onRenewUpdater,
        EntitySaveHelper $entitySaveHelper
    )
    {
        $this->massRenewProcess   = $massRenewProcess;
        $this->parametersProvider = $parametersProvider;
        $this->onRenewUpdater     = $onRenewUpdater;
        $this->entitySaveHelper   = $entitySaveHelper;
    }

    public function massRenew(array $subscriptions, CarrierInterface $carrier): MassRenewResult
    {
        $processed = 0;
        $error     = null;
        $failed    = [];
        $succeeded = [];

        /** @var Subscription[] $indexedSubscriptions */
        $indexedSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            if (!$subscription instanceof Subscription) {
                throw new \InvalidArgumentException(sprintf('%s is not instance of Subscription', get_class($subscription)));
            }
            $indexedSubscriptions[$subscription->getUuid()] = $subscription;
        }


        $parameters = $this->parametersProvider->provideParameters($subscriptions);
        $response   = $this->massRenewProcess->doMassRenew($parameters, $carrier);

        foreach ($response->data as $uuid => $result) {
            $subscription = $indexedSubscriptions[$uuid];
            $processId    = intval($result);
            $processed++;
            if ($processId) {
                $succeeded[$processId] = $subscription;
            } else {
                $failed[$processId] = $subscription;
            }
        }

        return new MassRenewResult($processed, $succeeded, $failed, $error);
    }
}