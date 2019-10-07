<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 9:05 PM
 */

namespace SubscriptionBundle\Subscription\Callback\Common\Handler;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

interface CallbackHandlerInterface
{
    public function doProcess(Subscription $subscription, ProcessResult $response): void;

    public function getPiwikEventName(): string;

    public function isSupport($type): bool;

    public function afterProcess(Subscription $subscription, ProcessResult $response): void;

    public function isActionAllowed(Subscription $subscription): bool;

}