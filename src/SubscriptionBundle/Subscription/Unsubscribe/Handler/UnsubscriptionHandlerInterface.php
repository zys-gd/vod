<?php

namespace SubscriptionBundle\Subscription\Unsubscribe\Handler;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

interface UnsubscriptionHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool;

    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool;

    public function applyPostUnsubscribeChanges(Subscription $subscription);

    public function getAdditionalUnsubscribeParameters(): array;
}