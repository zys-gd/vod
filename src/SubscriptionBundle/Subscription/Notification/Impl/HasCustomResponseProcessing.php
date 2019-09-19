<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.09.19
 * Time: 18:19
 */

namespace SubscriptionBundle\Subscription\Notification\Impl;


interface HasCustomResponseProcessing
{
    public function isResponseOk($result): bool;
}