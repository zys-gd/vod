<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:54
 */

namespace SubscriptionBundle\Subscription\Renew\Common;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\MassRenew\MassRenewer;
use SubscriptionBundle\Subscription\Renew\DTO\MassRenewResult;
use SubscriptionBundle\Subscription\Renew\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Renew\Handler\RenewHandlerProvider;

class CommonFlowHandler
{
    /**
     * @var MassRenewer
     */
    private $massRenewer;
    /**
     * @var SubscriptionRepository
     */
    private $repository;
    /**
     * @var RenewHandlerProvider
     */
    private $provider;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * CommonFlowHandler constructor.
     * @param MassRenewer            $massRenewer
     * @param SubscriptionRepository $repository
     * @param RenewHandlerProvider   $provider
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        MassRenewer $massRenewer,
        SubscriptionRepository $repository,
        RenewHandlerProvider $provider,
        EntityManagerInterface $entityManager
    )
    {
        $this->massRenewer   = $massRenewer;
        $this->repository    = $repository;
        $this->provider      = $provider;
        $this->entityManager = $entityManager;
    }

    public function process(CarrierInterface $carrier): MassRenewResult
    {


        $subscriptions = $this->repository->getExpiredSubscriptions($carrier);

        if (count($subscriptions)) {

            $result = $this->massRenewer->massRenew($subscriptions, $carrier);

            $renewHandler = $this->provider->getRenewer($carrier);

            foreach ($result->getSucceededSubscriptions() as $processId => $subscription) {

                if ($this->isRenewDateChanged($subscription)) {
                    // Fixing concurrency issues
                    continue;
                }

                $subscription->setStatus(Subscription::IS_ON_HOLD);
                $subscription->setError('not_fully_paid');

                if ($renewHandler instanceof HasCommonFlow) {
                    $renewHandler->onRenewSendSuccess($subscription, $processId);
                }
            }

            foreach ($result->getFailedSubscriptions() as $processId => $subscription) {

                if ($this->isRenewDateChanged($subscription)) {
                    // Fixing concurrency issues
                    continue;
                }

                if ($renewHandler instanceof HasCommonFlow) {
                    $renewHandler->onRenewSendFailure($subscription, $processId);
                }
            }

            $this->entityManager->flush();

            return $result;


        } else {
            return new MassRenewResult(0, [], [], null);
        }
    }

    private function isRenewDateChanged(Subscription $subscription): bool
    {
        $oldRenewDate = $subscription->getRenewDate();
        $this->entityManager->refresh($subscription);
        $newRenewDate = $subscription->getRenewDate();

        if ($oldRenewDate->format("Y-m-d") !== $newRenewDate->format("Y-m-d")) {
            return true;
        }

        return false;
    }

}