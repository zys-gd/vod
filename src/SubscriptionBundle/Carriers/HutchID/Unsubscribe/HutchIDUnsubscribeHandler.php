<?php

namespace SubscriptionBundle\Carriers\HutchID\Unsubscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerInterface;

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
     * @return array
     */
    public function getAdditionalUnsubscribeParameters(): array
    {
        return [];
    }

    /**
     * @param Subscription $subscription
     */
    public function applyPostUnsubscribeChanges(Subscription $subscription)
    {

    }
}