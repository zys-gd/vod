<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 13:33
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;


use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Entity\Subscription;
use IdentificationBundle\Entity\User;

class DefaultHandler implements HasCommonFlow, SubscriptionHandlerInterface
{

    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
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