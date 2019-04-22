<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\Locker;


interface LockInterface
{
    public function decrProcessingSlotsWithLock();
}