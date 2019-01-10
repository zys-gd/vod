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
use UserBundle\Entity\BillableUser;

class DefaultHandler implements HasCommonFlow, SubscriptionHandlerInterface
{

    public function canHandle(\AppBundle\Entity\Carrier $carrier): bool
    {
        return true;
    }

    public function getAdditionalSubscribeParams(Request $request, BillableUser $billableUser): array
    {
        return [];
    }

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        // TODO: Implement applyPostSubscribeChanges() method.
    }
}