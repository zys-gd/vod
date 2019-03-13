<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.03.19
 * Time: 15:22
 */

namespace SubscriptionBundle\Service\Action\MassRenew;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\MassRenewProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Renew\DTO\MassRenewResult;
use SubscriptionBundle\Service\Action\Renew\Handler\HasCommonFlow;
use SubscriptionBundle\Service\Action\Renew\Handler\RenewHandlerProvider;
use SubscriptionBundle\Service\Action\Renew\OnRenewUpdater;
use SubscriptionBundle\Service\EntitySaveHelper;

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
     * @var RenewHandlerProvider
     */
    private $renewHandlerProvider;


    /**
     * MassRenewer constructor.
     * @param MassRenewProcess            $massRenewProcess
     * @param MassRenewParametersProvider $parametersProvider
     * @param OnRenewUpdater              $onRenewUpdater
     * @param EntitySaveHelper            $entitySaveHelper
     * @param RenewHandlerProvider        $renewHandlerProvider
     */
    public function __construct(
        MassRenewProcess $massRenewProcess,
        MassRenewParametersProvider $parametersProvider,
        OnRenewUpdater $onRenewUpdater,
        EntitySaveHelper $entitySaveHelper,
        RenewHandlerProvider $renewHandlerProvider
    )
    {
        $this->massRenewProcess     = $massRenewProcess;
        $this->parametersProvider   = $parametersProvider;
        $this->onRenewUpdater       = $onRenewUpdater;
        $this->entitySaveHelper     = $entitySaveHelper;
        $this->renewHandlerProvider = $renewHandlerProvider;
    }

    public function massRenew(array $subscriptions, CarrierInterface $carrier): MassRenewResult
    {
        $processed = 0;
        $succeeded = 0;
        $failed    = 0;
        $error     = null;

        /** @var Subscription[] $indexedSubscriptions */
        $indexedSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            if (!$subscription instanceof Subscription) {
                throw new \InvalidArgumentException(sprintf('%s is not instance of Subscription', get_class($subscription)));
            }
            $indexedSubscriptions[$subscription->getUuid()] = $subscription;
        }


        $renewHandler = $this->renewHandlerProvider->getRenewer($carrier);
        $parameters   = $this->parametersProvider->provideParameters($subscriptions);
        $response     = $this->massRenewProcess->doMassRenew($parameters, $carrier);

        foreach ($response->data as $uuid => $result) {
            $subscription = $indexedSubscriptions[$uuid];
            $processId    = intval($result);
            $processed++;
            if ($processId) {
                $succeeded++;

                $subscription->setStatus(Subscription::IS_ON_HOLD);
                if ($renewHandler instanceof HasCommonFlow) {
                    $renewHandler->onRenewSend($subscription, $processId);
                }
            } else {
                $failed++;
                $this->onRenewUpdater->updateSubscriptionOnFailure($subscription, $result);
                if ($renewHandler instanceof HasCommonFlow) {
                    $renewHandler->onFailure($subscription, $result);
                }
            }
        }
        $this->entitySaveHelper->saveAll();

        return new MassRenewResult($processed, $succeeded, $failed, $error);
    }
}