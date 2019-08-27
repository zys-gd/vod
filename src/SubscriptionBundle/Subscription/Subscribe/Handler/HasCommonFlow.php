<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 11:54
 */

namespace SubscriptionBundle\Subscription\Subscribe\Handler;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;

interface HasCommonFlow
{
    public function getAdditionalSubscribeParams(Request $request, User $User): array;

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result);
}