<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 10:22
 */

namespace SubscriptionBundle\Carriers\OrangeEG\Unsubscribe;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerInterface;

class OrangeEGHandler implements UnsubscriptionHandlerInterface
{

    public function canHandle(Carrier $carrier): bool
    {
        return $carrier->getIdCarrier() == \AppBundle\Constant\Carrier::ORANGE_EGYPT;
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