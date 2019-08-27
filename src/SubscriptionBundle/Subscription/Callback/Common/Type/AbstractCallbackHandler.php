<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 9:05 PM
 */

namespace SubscriptionBundle\Subscription\Callback\Common\Type;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

abstract class AbstractCallbackHandler
{
    abstract public function updateSubscriptionByCallbackData(Subscription $subscription, ProcessResult $response);

    abstract public function getPiwikEventName(): string;

    abstract public function isSupport($type): bool;

    abstract public function afterProcess(Subscription $subscription, ProcessResult $response): void;

}