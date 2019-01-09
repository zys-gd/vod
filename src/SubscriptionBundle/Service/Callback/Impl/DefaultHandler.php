<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.10.18
 * Time: 9:52
 */

namespace SubscriptionBundle\Service\Callback\Impl;


use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use UserBundle\Entity\BillableUser;

class DefaultHandler implements CarrierCallbackHandlerInterface, HasCommonFlow
{
    public function canHandle(Request $request, int $carrierId): bool
    {
        return true;
    }

    public function afterProcess(Subscription $subscription, BillableUser $billableUser, ProcessResult $processResponse)
    {
        // TODO: Implement afterSuccess() method.
    }

}