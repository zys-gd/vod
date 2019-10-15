<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 10:12
 */

namespace SubscriptionBundle\Subscription\Unsubscribe\Handler;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;

class DefaultHandler implements UnsubscriptionHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
    }

    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool
    {
        return true;
    }

    public function applyPostUnsubscribeChanges(Subscription $subscription): void
    {
        // TODO: Implement applyPostUnsubscribeChanges() method.
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getAdditionalUnsubscribeParameters(Request $request): array
    {
        return [];
    }
}