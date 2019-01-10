<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 11:09
 */

namespace SubscriptionBundle\Carriers\EtisalatEG\Unsubscribe;


use AppBundle\Entity\Carrier;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerInterface;

class EtisalatEGUnsubscribeHandler implements UnsubscriptionHandlerInterface
{

    public function canHandle(Carrier $carrier): bool
    {
        return $carrier->getIdCarrier() === \AppBundle\Constant\Carrier::ETISALAT_EGYPT;
    }

    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool
    {
        return false;
    }

    public function applyPostUnsubscribeChanges(Subscription $subscription)
    {
        // TODO: Implement applyPostUnsubscribeChanges() method.
    }
}