<?php

namespace SubscriptionBundle\Carriers\HutchID\Unsubscribe;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class HutchIDUnsubscribeHandler implements UnsubscriptionHandlerInterface
{
    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return false; //$carrier->getBillingCarrierId() === ConstBillingCarrierId::HUTCH_INDONESIA;
    }

    /**
     * @param ProcessResult $processResult
     *
     * @return bool
     */
    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool
    {
        return true;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getAdditionalUnsubscribeParameters(Request $request): array
    {
        return [];
    }

    /**
     * @param Subscription $subscription
     */
    public function applyPostUnsubscribeChanges(Subscription $subscription): void
    {

    }
}