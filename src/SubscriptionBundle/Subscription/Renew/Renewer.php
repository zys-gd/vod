<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 15:12
 */

namespace SubscriptionBundle\Subscription\Renew;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\RenewProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Renewer
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var RenewProcess
     */
    private $renewProcess;
    /**
     * @var OnRenewUpdater
     */
    private $onRenewUpdater;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var RenewParametersProvider
     */
    private $parametersProvider;


    /**
     * Renewer constructor.
     * @param LoggerInterface          $logger
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntitySaveHelper         $entitySaveHelper
     * @param RenewProcess             $renewProcess
     * @param OnRenewUpdater           $onRenewUpdater
     * @param RenewParametersProvider  $parametersProvider
     */
    public function __construct(
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
        EntitySaveHelper $entitySaveHelper,
        RenewProcess $renewProcess,
        OnRenewUpdater $onRenewUpdater,
        RenewParametersProvider $parametersProvider
    )
    {
        $this->logger             = $logger;
        $this->entitySaveHelper   = $entitySaveHelper;
        $this->renewProcess       = $renewProcess;
        $this->onRenewUpdater     = $onRenewUpdater;
        $this->eventDispatcher    = $eventDispatcher;
        $this->parametersProvider = $parametersProvider;
    }

    public function renew(Subscription $subscription): ProcessResult
    {
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_RENEW);
        $this->entitySaveHelper->persistAndSave($subscription);

        try {
            $parameters = $this->parametersProvider->provideParameters($subscription);
            $response   = $this->renewProcess->doRenew($parameters);

            $this->onRenewUpdater->updateSubscriptionByResponse($subscription, $response);

            return $response;
        } catch (\SubscriptionBundle\BillingFramework\Process\Exception\RenewingProcessException $exception) {

            $subscription->setStatus(Subscription::IS_ERROR);
            $subscription->setError('renewing_process_exception');
            throw $exception;

        } finally {
            $this->entitySaveHelper->persistAndSave($subscription);
        }
    }
}