<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 13:33
 */

namespace SubscriptionBundle\Subscription\Subscribe\Handler;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;

class DefaultHandler implements HasCommonFlow, SubscriptionHandlerInterface
{

    public function canHandle(\CommonDataBundle\Entity\Interfaces\CarrierInterface $carrier): bool
    {
        return true;
    }

    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        return [];
    }

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        // TODO: Implement applyPostSubscribeChanges() method.
    }
}