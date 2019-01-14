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
use IdentificationBundle\Entity\User;

interface HasCommonFlow
{
    public function getAdditionalSubscribeParams(Request $request, User $User): array;

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result);
}