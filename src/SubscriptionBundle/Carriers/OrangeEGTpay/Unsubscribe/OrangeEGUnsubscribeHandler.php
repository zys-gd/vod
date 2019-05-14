<?php

namespace SubscriptionBundle\Carriers\OrangeEGTpay\Unsubscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerInterface;

/**
 * Class VodafoneEGUnsubscribeHandler
 */
class OrangeEGUnsubscribeHandler implements UnsubscriptionHandlerInterface
{
    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param ProcessResult $processResult
     *
     * @return bool
     */
    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool
    {
        return false;
    }

    /**
     * @param Subscription $subscription
     */
    public function applyPostUnsubscribeChanges(Subscription $subscription)
    {
        // TODO: Implement applyPostUnsubscribeChanges() method.
    }
}