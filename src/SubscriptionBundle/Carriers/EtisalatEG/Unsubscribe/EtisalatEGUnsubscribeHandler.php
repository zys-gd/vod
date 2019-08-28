<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 11:09
 */

namespace SubscriptionBundle\Carriers\EtisalatEG\Unsubscribe;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerInterface;

class EtisalatEGUnsubscribeHandler implements UnsubscriptionHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ETISALAT_EGYPT;
    }

    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool
    {
        return false;
    }

    public function applyPostUnsubscribeChanges(Subscription $subscription)
    {
        // TODO: Implement applyPostUnsubscribeChanges() method.
    }

    public function getAdditionalUnsubscribeParameters(): array
    {
        return [];
    }
}