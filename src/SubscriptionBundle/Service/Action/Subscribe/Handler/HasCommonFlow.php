<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 11:54
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;


use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Entity\Subscription;
use UserBundle\Entity\BillableUser;

interface HasCommonFlow
{
    public function getAdditionalSubscribeParams(Request $request, BillableUser $billableUser): array;

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result);
}