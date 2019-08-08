<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 11:09
 */

namespace SubscriptionBundle\Carriers\EtisalatEG\Unsubscribe;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerInterface;

class EtisalatEGUnsubscribeHandler implements UnsubscriptionHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::ETISALAT_EGYPT;
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