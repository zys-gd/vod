<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 10:06
 */

namespace SubscriptionBundle\Service\Action\Unsubscribe\Handler;


use AppBundle\Entity\Carrier;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

interface UnsubscriptionHandlerInterface
{
    public function canHandle(Carrier $carrier): bool;

    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool;

    public function applyPostUnsubscribeChanges(Subscription $subscription);

}